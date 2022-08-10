<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\SupportTicket;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

final class SupportTicketCreatedSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['createSupportTicket', EventPriorities::PRE_WRITE],
        ];
    }

    public function createSupportTicket(ViewEvent $event): void
    {
        /** @var User $resource */
        $resource = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        /** @var SupportTicket $resource */
        if ($resource instanceof SupportTicket && Request::METHOD_POST === $method) {
            /** @var User $user */
            $user = $this->security->getUser();

            $resource->setEmail($user->getEmail());
        }
    }
}
