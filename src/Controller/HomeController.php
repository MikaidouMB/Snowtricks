<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Trick;
use App\Form\MessageType;
use App\Repository\ImageRepository;
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
    public function index(ManagerRegistry $doctrine,$page,$nbre,Request $request): Response
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
     * @Route("trick/{slug}/{page?1}/{nbre?10}", name="trick_show", methods={"GET","POST"},requirements={"page"="\d+"})
     * @param Trick $trick
     * @param \App\Repository\ImageRepository $imageRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ManagerRegistry $doctrine
     * @param $page
     * @param $nbre
     * @param $slug
     * @return Response
     */
    public function show(Trick $trick,
                         ImageRepository  $imageRepository,
                         Request $request,
                         EntityManagerInterface $entityManager,
                         ManagerRegistry $doctrine,
                         $page, $nbre,$slug): Response
    {
        //dd($request,$page, $nbre, $slug);
        $message = new Message();
        $message->setUser($trick->getUser());
        $message->setTrick($trick);

        $messagesPaginatedByTrick = $doctrine->getRepository(Message::class);
        $messages = $messagesPaginatedByTrick->findBy( ['trick'=> $trick->getId()],['createdAt' => 'DESC'],10,($page -1) * $nbre);

        $allMessagesByFigure = $messagesPaginatedByTrick->findBy(['trick'=> $trick->getId()]);
        $nbMessages = count($allMessagesByFigure);
        $nbrePage = ceil($nbMessages / $nbre);

        $form = $this->createForm(MessageType::class, $message)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute("trick_show", ["id"=>$trick->getId()]);
        }
        return $this->render('trick/show.html.twig', [
            'messages'=>$messages,
            'images'=> $imageRepository->findImagesByTrick($trick),
            'trick' => $trick,
            'isPaginated' => true,
            'nbrePage' => (string)$nbrePage,
            'page' => $page,
            'nbre' => $nbre,
            'form' => $form->createView()
        ]);
    }
}