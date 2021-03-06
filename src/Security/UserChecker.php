<?php

namespace App\Security;

use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }
        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException(
                "Votre compte n'est pas actif, veuillez consulter vos e-mails pour l'activer avant le 
                {$user->getAccountMustBeVerifiedBefore()->format('d/m/Y à H\hi')}");
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }
        // user account is expired, the user may be notified
        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException('mail non validé.');
        }
    }
}