<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/delete/user/{id}", name="user_delete", methods={"DELETE", "GET", "POST"})
     */
    public function deleteUser(User $user,
                               EntityManagerInterface $entityManager,
                               Request $request,
                               CsrfTokenManagerInterface $csrfTokenManager): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $token = new CsrfToken('delete-user', $request->query->get('_csrf_token'));
        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash(
            'success',
            'La compte a bien été supprimée'
        );
        return $this->redirectToRoute('app_home',[], \Symfony\Component\HttpFoundation\Response::HTTP_SEE_OTHER);
    }
}