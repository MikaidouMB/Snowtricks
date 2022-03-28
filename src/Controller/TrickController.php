<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Message;
use App\Entity\Trick;
use App\Entity\Videos;
use App\Form\MessageType;
use App\Form\TrickType;
use App\Repository\ImageRepository;
use App\Repository\MessageRepository;
use App\Repository\TrickRepository;
use App\Repository\VideosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/trick")
 */
class TrickController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private VideosRepository  $videosRepository;

    public function __construct(ManagerRegistry $doctrine, VideosRepository $videosRepository) {
        $this->doctrine = $doctrine;
        $this->videosRepository =$videosRepository;
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
     * @Route("/new", name="trick_new", methods={"GET", "POST"})
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
            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="trick_edit", methods={"GET", "POST", "DELETE"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Trick $trick
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param VideosRepository $videosRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, Trick $trick,
                         EntityManagerInterface $entityManager,VideosRepository $videosRepository
    ): Response
    {

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        $idTrick = $trick->getId();

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


            return $this->redirectToRoute('app_home',[],Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'video' =>$videosRepository->findByVideosTrick($trick),
            'form' => $form,
        ]);
    }

    /**
     * @param Images $images
     * @param Int $idTrick
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/delete/{idTrick}/{id}", name="trick_delete_image", methods={"DELETE", "GET", "POST"})
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
