<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Trick;
use App\Form\MessageType;
use App\Repository\ImageRepository;
use App\Repository\MessageRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    /**
     * @Route("/trick-{id}", name="trick_show", methods={"GET","POST"})
     * @param Trick $trick
     * @param \App\Repository\MessageRepository $messageRepository
     * @param \App\Repository\ImageRepository $imageRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function show(Trick $trick,
                         MessageRepository $messageRepository,
                         ImageRepository  $imageRepository,
                         Request $request,
                         EntityManagerInterface $entityManager
    ): Response
    {
        $message = new Message();
        $message->setUser($trick->getUser());
        $message->setTrick($trick);
        $form = $this->createForm(MessageType::class, $message)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();
            return $this->redirectToRoute("trick_show", ["id"=>$trick->getId()]);
        }
        return $this->render('trick/show.html.twig', [
            'messages'=>$messageRepository->findMessageByTrick($trick),
            'images'=> $imageRepository->findImagesByTrick($trick),
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="trick_delete", methods={"POST"})
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
