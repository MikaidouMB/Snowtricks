<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Trick;
use App\Entity\Videos;
use App\Form\TrickType;
use App\Repository\VideosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * @Route("/trick")
 */
class TrickController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private VideosRepository  $videosRepository;
    private SluggerInterface $slugger;
    private AccessDecisionManagerInterface $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager,
                               ManagerRegistry $doctrine, VideosRepository $videosRepository, SluggerInterface $slugger)
    {
        $this->doctrine = $doctrine;
        $this->videosRepository =$videosRepository;
        $this->slugger = $slugger;
        $this->accessDecisionManager = $accessDecisionManager;

    }

    /**
     * @Route("/new", name="trick_new", methods={"GET", "POST"}, priority=1)
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrfTokenManager
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $entityManager,
                        CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = new CsrfToken('new-trick', $request->query->get('_csrf_token'));
        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            foreach ($images as $image){

                $file = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $file
                );
                $img = new Images();
                $img->setName($file);
                $trick->addImage($img);
            }
            $trick->setUser($this->getUser());
            $slug = $request->request->filter('trick')['nameFigure'];
            $trick->setSlug($this->slugger->slug($slug));
            $entityManager->persist($trick);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'La figure a bien été crée'
            );
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="trick_edit", methods={"GET", "POST", "DELETE"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Trick $trick
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param VideosRepository $videosRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, Trick $trick,
                         EntityManagerInterface $entityManager,
                         VideosRepository $videosRepository,
    ): Response
    {
        if (!$this->getUser()){
            $this->denyAccessUnlessGranted('edit',$trick);
        }else{
            $token = new UsernamePasswordToken($this->getUser(), 'none', 'none',
                $this->getUser()->getRoles());
            if(!$this->accessDecisionManager->decide($token, (array)'ROLE_ADMIN') and
                !$this->accessDecisionManager->decide($token, (array)'ROLE_MODO')
                and
                $this->getUser()->getUserIdentifier() !== $trick->getUser()->getUserIdentifier()){
                $this->denyAccessUnlessGranted('edit',$trick);
            }
        }
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            foreach ($images as $image){
                $file = md5(uniqid()) . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('images_directory'),
                    $file
                );
                $img = new Images();
                $img->setName($file);
                $trick->addImage($img);
                }
            $trick->setUser($this->getUser());
            $entityManager->persist($trick);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le trick a bien été modifiée'
            );
            return $this->redirectToRoute('app_home',[],Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'video' =>$videosRepository->findByVideosTrick($trick),
            'form' => $form,
        ]);
    }

    /**
     * @param Trick $trick
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @return Response
     * @Route("/delete-trick/{slug}", name="trick_delete",requirements={"slug"=".+"}, methods={"DELETE", "GET", "POST"})
     */
    public function deleteTrick( Trick $trick, Request $request,
                                EntityManagerInterface $entityManager,
                                 CsrfTokenManagerInterface $csrfTokenManager):Response
    {
        $token = new CsrfToken('delete-trick', $request->query->get('_csrf_token'));
        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }
            $entityManager->remove($trick);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le trick a bien été supprimée'
            );
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

    /**
     * @param Images $images
     * @param String $slug
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/delete/{slug}/{id}", name="trick_delete_image_show", methods={"DELETE", "GET", "POST"})
     */
    public function deleteImageShow(Images $images, $slug,
                                EntityManagerInterface $entityManager,
                                ): Response
    {
        $entityManager->remove($images);
        $entityManager->flush();
        $this->addFlash(
            'success',
            'L\'image a bien été supprimée'
        );
        return $this->redirectToRoute('trick_show',['slug'=> $slug],Response::HTTP_SEE_OTHER);
    }

    /**
     * @param \App\Entity\Images $image
     * @param EntityManagerInterface $entityManager
     * @param $slug
     * @return Response
     * @Route("/delete/edit/{slug}/{image}", name="trick_delete_image_edit", methods={"DELETE", "GET", "POST"})
     */
    public function deleteImageEdit(Images $image,
                                EntityManagerInterface $entityManager,$slug,
                               ):Response
    {
        $entityManager->remove($image);
            $entityManager->flush();
        $this->addFlash(
            'success',
            'L\'image a bien été supprimée'
        );
        return $this->redirectToRoute('trick_edit',['slug'=> $slug],Response::HTTP_SEE_OTHER);
    }

    /**
     * @param Videos|null $video_id
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param $slug
     * @param \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrfTokenManager
     * @return Response
     * @Route("/delete/video/{slug}/{video_id}", name="trick_delete_video", methods={"DELETE", "GET", "POST"})
     */
    public function deleteVideo(Videos $video_id = null, Request $request,
                                EntityManagerInterface $entityManager,$slug,
                                CsrfTokenManagerInterface $csrfTokenManager):Response
    {
        $token = new CsrfToken('delete-video', $request->query->get('_csrf_token'));
        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }
        $entityManager->remove($video_id);
        $entityManager->flush();
        $this->addFlash(
            'success',
            'La vidéo a bien été supprimée'
        );
        return $this->redirectToRoute('trick_show',['slug'=> $slug],Response::HTTP_SEE_OTHER);
    }
}
