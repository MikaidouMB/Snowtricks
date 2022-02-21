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
use Doctrine\ORM\EntityManagerInterface;
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
    public function new(Request $request,
                        EntityManagerInterface $entityManager
                      ): Response
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
            $trick->addVideo($video);
            dd($trick->addVideo($video));
                //$fileName = $fileUploader->upload($photo);
            $trick->setUser($this->getUser());
            //dd($videos);
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
     * @Route("/{id}", name="trick_show", methods={"GET","POST"})
     * @param Trick $trick
     * @param \App\Repository\TrickRepository $trickRepository
     * @param \App\Repository\MessageRepository $messageRepository
     * @param \App\Repository\ImageRepository $imageRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function show(Trick $trick,
                         TrickRepository $trickRepository,
                         MessageRepository $messageRepository,
                         ImageRepository  $imageRepository,
                         Request $request,
                         EntityManagerInterface $entityManager
    ): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setUser($trick->getUser());
            $message->setTrick($trick);
            $entityManager->persist($message);
            $entityManager->flush();
        }
            return $this->render('trick/show.html.twig', [
                'messages'=>$messageRepository->findMessageByTrick($trick),
                //'messages'=> $messageRepository->findMessageByTrick($trick),
                'images'=> $imageRepository->findImagesByTrick($trick),
                //'images'=> dd($imageRepository->findImagesByTrick($trick)),
                'trick' => $trick,
                'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="trick_edit", methods={"GET", "POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Trick $trick
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, Trick $trick,
                         EntityManagerInterface $entityManager
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
            $trick->setUser($this->getUser());
            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="trick_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        return $this->redirectToRoute('trick_index', [], Response::HTTP_SEE_OTHER);
    }
}
