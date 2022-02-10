<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\SendEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function register(Request $request,
                             UserPasswordHasherInterface $userPasswordHasher,
                             EntityManagerInterface $entityManager,
                             TokenGeneratorInterface $tokenGenerator,
                             SendEmail $sendEmail
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registrationToken = $tokenGenerator->generateToken();
            // encode the plain password
            $user->setRegistrationToken($registrationToken)
                 ->setPassword($userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData()));

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $sendEmail->send([
                'recipient_email'=> $user->getEmail(),
                'subject'=> 'Verification de votre adresse email pour activer votre compte',
                'html_template' => "registration/confirmation_email.html.twig",
                'context' =>[
                    'userID' => $user->getId(),
                    'registrationToken'=>$registrationToken,
                    'tokenLifeTime' => $user->getAccountMustBeVerifiedBefore()->format('d/m/Y à H:i')
                ]
            ]);
            $this->addFlash('success',"Votre compte utilisateur a bien été créé, veuillez consulter vos email pour l'activer");

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id<\d+>}/{token}", name="app_verify_account", methods={"GET"})
     */
    public function verifyUserAccount(
        EntityManagerInterface $entityManager,
        User $user,
        string $token
    ):Response
    {
        if(($user->getRegistrationToken() === null) ||($user->getRegistrationToken() !== $token ||
                ($this->isNotRequestedInTime($user->getAccountMustBeVerifiedBefore())))){
            throw new AccessDeniedException();
        }
        $user->setIsVerified(true);

        $user->setAccountVerifiedAt(new \DateTimeImmutable('now'));

        $user->setRegistrationToken(null);

        $entityManager->flush();

        $this->addFlash('success', 'Votre compte utilisateur est désormais activé vous pouvez vous connecter');

        return $this->redirectToRoute('app_login');
    }

    private function isNotRequestedInTime(\DateTimeImmutable $accountMustBeVerifiedBefore):bool
    {
        return (new \DateTimeImmutable('now')) > $accountMustBeVerifiedBefore;
    }
}
