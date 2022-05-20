<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("/message")
 */
class MessageController extends AbstractController
{
    /**
     * @Route("/delete/{id}/", name="message_delete", methods={"DELETE", "GET", "POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Message $message
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrfTokenManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Request $request,Message $message, EntityManagerInterface $entityManager,
                           CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = new CsrfToken('delete-message', $request->query->get('_csrf_token'));
        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }
        $entityManager->remove($message);
        $entityManager->flush();
        $this->addFlash(
            'success',
            'Le commentaire a bien été supprimée'
        );
        return $this->redirectToRoute('admin_comments',[],Response::HTTP_SEE_OTHER);
    }
}
