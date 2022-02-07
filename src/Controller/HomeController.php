<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
