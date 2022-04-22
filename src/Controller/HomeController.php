<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Trick;
use App\Form\MessageType;
use App\Repository\ImageRepository;
use App\Repository\MessageRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home/{page?1}/{nbre?15}", name="app_home")
     */
    public function index(ManagerRegistry $doctrine,$page,$nbre): Response
    {
        $repository = $doctrine->getRepository(Trick::class);
        $tricks = $repository->findBy( [],[],$nbre,($page -1) * $nbre);
        $nbTricks = $repository->count([]);
        $nbrePage = ceil($nbTricks / $nbre);
        return $this->render('index.html.twig',[
            'tricks' => $tricks,
            'isPaginated' => true,
            'nbrePage' => (string)$nbrePage,
            'page' => $page,
            'nbre' => $nbre
        ]);
    }

    /**
     * @Route("/trick-{id}/{page?1}/{nbre?5}", name="trick_show", methods={"GET","POST"})
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
                         EntityManagerInterface $entityManager,
                         ManagerRegistry $doctrine,
                         $page, $nbre
    ): Response
    {
        $message = new Message();
        $message->setUser($trick->getUser());
        $message->setTrick($trick);

        //$repository = $doctrine->getRepository(Message::class)->findMessageByTrick($trick);
        $messagesPaginatedByTrick = $doctrine->getRepository(Message::class)->findBy( [],[],$nbre,($page -1) * $nbre);

        foreach ($messagesPaginatedByTrick as $message){
            $nbMessages = $message->count([]);
        }
        $nbrePage = ceil($nbMessages / $nbre);


        $form = $this->createForm(MessageType::class, $message)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute("trick_show", ["id"=>$trick->getId()]);
        }
        return $this->render('trick/show.html.twig', [
            'messages'=>$messagesPaginatedByTrick,
            'images'=> $imageRepository->findImagesByTrick($trick),
            'trick' => $trick,
            'isPaginated' => true,
            'nbrePage' => (string)$nbrePage,
            'page' => $page,
            'nbre' => $nbre,
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
