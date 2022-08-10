<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        /** @var User $user */
        $user = $event->getUser();

        $payload = $event->getData();

        $payload['@id'] = '/users/' . $user->getId();
        $payload['email'] = $user->getEmail();
        $payload['givenName'] = $user->getGivenName();
        $payload['familyName'] = $user->getFamilyName();
        $payload['roles'] = $user->getRoles();

        $event->setData($payload);
    }
}