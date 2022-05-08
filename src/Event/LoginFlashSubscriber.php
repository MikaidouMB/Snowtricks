<?php

namespace App\Event;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Intervient lorsqu'une connexion s'est bien passée pour ajouter un flash de succes ! :)
 */
class LoginFlashSubscriber implements EventSubscriberInterface
{
    /**
     * Le flashbag qui contient les message de succes / erreur
     *
     * @var FlashBagInterface
     */
    protected FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    #[ArrayShape([SecurityEvents::INTERACTIVE_LOGIN => "string[]"])] public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => ['addFlashToSession']
        ];
    }

    public function addFlashToSession(InteractiveLoginEvent $event)
    {
        $this->flashBag->add('success', "Bravo, vous êtes connecté !");
    }
}