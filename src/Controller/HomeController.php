<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Message;
use App\Entity\Photo;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\EditUserType;
use App\Form\MessageType;
use App\Form\UserType;
use App\Repository\ImageRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class HomeController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/home/{page?1}/{nbre?15}", name="app_home",requirements={"page"="\d+"})
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
     * @param UserRepository $userRepository
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
                         UserRepository $userRepository,
                         Security $security,
                         $page, $nbre,$slug): Response
    {
        $message = new Message();
        $message->setUser($this->getUser());

        $message->setTrick($trick);



        $messagesPaginatedByTrick = $doctrine->getRepository(Message::class);
        $messages = $messagesPaginatedByTrick->findBy( ['trick'=> $trick->getId()],['createdAt' => 'DESC'],10,($page -1) * $nbre);
        $allMessagesByFigure = $messagesPaginatedByTrick->findBy(['trick'=> $trick->getId()]);
        $nbMessages = count($allMessagesByFigure);
        $nbrePage = ceil($nbMessages / $nbre);

        //Il faut récupérer les USERS.username = Message.author
        //dump($userRepository->findUsersByMessage($message));

        $form = $this->createForm(MessageType::class, $message)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //dd($message);

            //$user = $this->getUser();
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute("trick_show", ["slug"=>$trick->getSlug()]);
        }
        return $this->render('trick/show.html.twig', [
            'messages'=>$messages,
            'authors'=> $userRepository->findUsersByMessage($message),
            'images'=> $imageRepository->findImagesByTrick($trick),
            //dd($imageRepository->findImagesByTrick($trick)),
            'trick' => $trick,
            'isPaginated' => true,
            'nbrePage' => (string)$nbrePage,
            'page' => $page,
            'nbre' => $nbre,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/home/account/{id}", name="user_account", methods={"GET","POST"}, priority = 2)
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $userPasswordHasher
     * @param \Doctrine\Persistence\ManagerRegistry $doctrine
     * @param \App\Entity\User $user
     * @return Response
     */
    public function showUserAccount( Request $request,
                                     EntityManagerInterface $entityManager,
                                     UserPasswordHasherInterface $userPasswordHasher,
                                     ManagerRegistry $doctrine, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('password')->getData() != null){
                $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('password')->getData()));
            }
            /**@var UploadedFile $photo*/
            $photo = $form->get('photos')->getData();
            //dd($photo->getClientOriginalName());
            if ($photo != null) {
                    $file = md5(uniqid()) . '.' . $photo->guessExtension();
                    $photo->move(
                        $this->getParameter('images_directory'),
                        $file
                    );
                    $namePhoto = $photo->getClientOriginalName();
                    $user->setPhoto($namePhoto);
            }
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Votre profil a bien été modifiée'
            );
            return $this->redirectToRoute("app_home", []);
        }
        return $this->render('user/user_account.html.twig', [
            'user'=>$user,
            'formUserProfil' => $form->createView()
        ]);
    }
}