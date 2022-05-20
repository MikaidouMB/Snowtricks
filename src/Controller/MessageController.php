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
     * @Route("/", name="message_index", methods={"GET"})
     */
    public function index(MessageRepository $messageRepository): Response
    {
        return $this->render('message/index.html.twig', [
            'messages' => $messageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="message_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('message/new_message.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="message_show", methods={"GET"})
     */
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="message_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('message_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('message/edit.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

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
