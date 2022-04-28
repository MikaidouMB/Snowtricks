<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Trick;
use App\Entity\Videos;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use App\Repository\VideosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/trick")
 */
class TrickController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private VideosRepository  $videosRepository;
    private SluggerInterface $slugger;

    public function __construct(ManagerRegistry $doctrine, VideosRepository $videosRepository, SluggerInterface $slugger) {
        $this->doctrine = $doctrine;
        $this->videosRepository =$videosRepository;
        $this->slugger = $slugger;

    }

    /**
     * @Route("/", name="trick_index", methods={"GET"})
     * @param TrickRepository $trickRepository
     * @return Response
     */
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="trick_new", methods={"GET", "POST"}, priority=1)
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
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
            $video = new Videos();
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
     * @Route("/{slug}/edit", name="trick_edit", methods={"GET", "POST", "DELETE"},requirements={"slug"="[a-zA-Z1-9\-_\/]+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Trick $trick
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param VideosRepository $videosRepository
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, Trick $trick,
                         EntityManagerInterface $entityManager,VideosRepository $videosRepository,string $slug
    ): Response
    {
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
            $video = new Videos();

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
     * @param $slug
     * @return Response
     * @Route("/delete-trick/{slug}", name="trick_delete",requirements={"slug"=".+"}, methods={"DELETE", "GET", "POST"})
     */
    public function deleteTrick( Trick $trick, Request $request,
                                EntityManagerInterface $entityManager,$slug):Response
    {
        // if ($this->isCsrfTokenValid('trick_delete_image'.$images->getId(), $request->request->get('_token'))) {
        //$idTrick = $trick->getId();
        $entityManager->remove($trick);
        $entityManager->flush();
        $this->addFlash(
            'success',
            'Le trick a bien été supprimée'
        );
        return $this->redirectToRoute('app_home',[],Response::HTTP_SEE_OTHER);
    }

    /**
     * @param Images $images
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/delete/{slug}/{id}", name="trick_delete_image_show", methods={"DELETE", "GET", "POST"})
     */
    public function deleteImageShow(Images $images,String $slug, Request $request,
                                EntityManagerInterface $entityManager):
    Response
    {
        // if ($this->isCsrfTokenValid('trick_delete_image'.$images->getId(), $request->request->get('_token'))) {
        $entityManager->remove($images);
        $entityManager->flush();
        $this->addFlash(
            'success',
            'L\'image a bien été supprimée'
        );
        return $this->redirectToRoute('trick_show',['slug'=> $slug],Response::HTTP_SEE_OTHER);
    }

    /**
     * @param Images $images
     * @param Int $idTrick
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/delete/{slug}/{id}", name="trick_delete_image", methods={"DELETE", "GET", "POST"})
     */
    public function deleteImage(Images $images,Int $idTrick, Request $request,
                                EntityManagerInterface $entityManager):Response
    {
       // if ($this->isCsrfTokenValid('trick_delete_image'.$images->getId(), $request->request->get('_token'))) {
            $entityManager->remove($images);
            $entityManager->flush();
        $this->addFlash(
            'success',
            'L\'image a bien été supprimée'
        );
        return $this->redirectToRoute('trick_edit',['id'=> $idTrick],Response::HTTP_SEE_OTHER);
    }

    /**
     * @param Videos|null $video_id
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/delete/{video_id}", name="trick_delete_video", methods={"DELETE", "GET", "POST"})
     */
    public function deleteVideo(Videos $video_id = null, Request $request,
                                EntityManagerInterface $entityManager
    ):Response
    {
        $idTrick = $video_id->getTrick()->getId();
        // if ($this->isCsrfTokenValid('trick_delete_image'.$images->getId(), $request->request->get('_token'))) {
        $entityManager->remove($video_id);
        $entityManager->flush();
        $this->addFlash(
            'success',
            'La vidéo a bien été supprimée'
        );
        return $this->redirectToRoute('trick_edit',['id'=> $idTrick],Response::HTTP_SEE_OTHER);
    }
}
