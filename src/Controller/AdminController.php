<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditProfilUserType;
use App\Repository\UserRepository;
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
     * Liste utilisateurs du site
     * @Route("/users", name="users")
     */
    public function usersList(UserRepository $users): Response
    {
        return $this->render("admin/users.html.twig", [
            'users'=> $users->findAll()
        ]);
    }

    /**
     * Modifier un utilisateur
     * @Route("/user/modify/{id}",name="modify_user")
     */
    public function editUser(User $user, Request $request){
        $form = $this->createForm(EditProfilUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('admin_users');
        }
        return $this->render('admin/editUser.html.twig', [
            'userForm' => $form->createView()
        ]);

    }
}
