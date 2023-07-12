<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\MessageType;
use App\Form\UserType;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Knp\Component\Pager\PaginatorInterface;


class HomeController extends AbstractController
{

    /**
     * @Route("/home/{page?1}/{nbre?15}", name="app_home",requirements={"page"="\d+"})
     */
    public function index(Request $request,ManagerRegistry $doctrine,$page = 1, $nbre = 15): Response
    {
        
        $repository = $doctrine->getRepository(Trick::class);
        $tricks = $repository->findBy( [],['createdAt' => 'DESC'],$nbre,($page -1) * $nbre);
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
     * @param PaginatorInterface $paginator
     * @param $page
     * @param $nbre
     * @return Response
     */
    public function show(Trick $trick,
                        ImageRepository $imageRepository,
                        Request $request,
                        EntityManagerInterface $entityManager,
                        PaginatorInterface $paginator,
                        $page, $nbre): Response
    {
        $message = new Message();
        $message->setUser($this->getUser());
        $message->setTrick($trick);

        $messagesQuery = $entityManager->getRepository(Message::class)->queryAllByTrick($trick);
        $messages = $paginator->paginate($messagesQuery, $page, $nbre);

        $form = $this->createForm(MessageType::class, $message)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute("trick_show", ["slug"=>$trick->getSlug()]);
        }
        return $this->render('trick/show.html.twig', [
            'messages'=>$messages,
            'images'=> $imageRepository->findImagesByTrick($trick),
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/home/account/{id}", name="user_account", methods={"GET","POST"}, priority = 2)
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $userPasswordHasher
     * @param \App\Entity\User $user
     * @param \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrfTokenManager
     * @return Response
     */
    public function showUserAccount( Request $request,
                                     EntityManagerInterface $entityManager,
                                     UserPasswordHasherInterface $userPasswordHasher,
                                     User $user,CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = new CsrfToken('account-edit', $request->query->get('_csrf_token'));
        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('password')->getData() != null){
                $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('password')->getData()));
            }
            /**@var UploadedFile $photo*/
            $photo = $form->get('photos')->getData();
            if ($photo != null) {
                    $file = md5(uniqid()) . '.' . $photo->guessExtension();
                    $photo->move(
                        $this->getParameter('images_directory'),
                        $file
                    );
                $user->setPhoto($file);
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