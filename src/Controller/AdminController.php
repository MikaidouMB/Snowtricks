<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\EditUserType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */

class AdminController extends AbstractController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/indexTricks/{page?1}/{nbre?8}", name="trick_index", methods={"GET"})
     * @param \Doctrine\Persistence\ManagerRegistry $doctrine
     * @param $page
     * @param $nbre
     * @return Response
     */
    public function indexTricks(ManagerRegistry $doctrine, $page, $nbre): Response
    {
        $repository = $doctrine->getRepository(Trick::class);
        $tricks = $repository->findBy([], [], $nbre, ($page - 1) * $nbre);

        /** @var \Doctrine\Persistence\ObjectRepository $nbTricks */
        $nbTricks = $repository->count([]);

        $nbrePage = ceil($nbTricks / $nbre);
        return $this->render('trick/index.html.twig', [
            'tricks' => $tricks,
            'isPaginated' => true,
            'nbrePage' => (string)$nbrePage,
            'page' => $page,
            'nbre' => $nbre]);
    }

    /**
     * Liste utilisateurs du site
     * @Route("/users/", name="users")
     */
    public function usersList(UserRepository $userRepository,
                              MessageRepository $messageRepository): Response
    {
        $messagesNotValidated = $messageRepository->findBy(array('isValidated'=> null));
        return $this->render("admin/users.html.twig", [
            'users' => $userRepository->findAll(),
            'messagesNotValidated' => $messagesNotValidated
        ]);
    }
    /**
     * Liste commentaires postés
     * @Route("/comments/", name="comments")
     */
    public function commentsList(MessageRepository $messageRepository,
                                 ManagerRegistry $doctrine,
    ): Response
    {
        $messageNotValidated = $doctrine->getRepository(Message::class);
        $messagesNotValidated = $messageNotValidated->findBy(['isValidated' => null ]);
        $nbMessages = count($messagesNotValidated);
        return $this->render("admin/comments_list.html.twig", [
            'messages' => $messageRepository->findAll(),
            'nbMessages'=> $nbMessages
        ]);
    }
    /**
     * Modifier un utilisateur
     * @Route("/user/modify/{id}",name="modify_user")
     */
    public function editUser(User $user, Request $request): Response
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('admin_users');
        }
        return $this->render('admin/editUser.html.twig', [
            'userForm' => $form->createView()
        ]);
    }

    /**
     * Validation d'un message utilisateur
     * @Route("/valid/{id}", name="message_validation", methods={"DELETE", "GET", "POST"})
     * @param \App\Entity\Message $message
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validation(Message $message, EntityManagerInterface $entityManager): Response
    {
       $message->setIsValidated(true);
        $entityManager->flush();
        return $this->redirectToRoute('admin_comments',[],Response::HTTP_SEE_OTHER);
    }
}