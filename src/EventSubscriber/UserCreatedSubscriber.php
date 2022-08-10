<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\FlowXo\FlowXoClient;
use App\Util\MailjetHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

final class UserCreatedSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $twig;
    private $entityManager;
 
    private MailjetHelper $mailjetHelper;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, Environment $twig, MailjetHelper $mailjetHelper)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->twig = $twig;
        $this->mailjetHelper = $mailjetHelper;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['createNewSocietyUser', EventPriorities::POST_WRITE],
        ];
    }

    public function createNewSocietyUser(ViewEvent $event): void
    {
        $resource = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        /** @var User $resource */
        if ($resource instanceof User && Request::METHOD_POST === $method) {

            $isClient = in_array('ROLE_CLIENT', $resource->getRoles());
            $isAgent = (in_array('ROLE_AGENT', $resource->getRoles())
                || in_array('ROLE_AGENT_LEAD', $resource->getRoles())
                || in_array('ROLE_AGENT_LEAD_CITY', $resource->getRoles())
                || in_array('ROLE_AGENT_LIVE_CHAT', $resource->getRoles()));

            try {
                $subject = 'Client confirmation';
                $message['html'] = $this->twig->render('email_templates/user/created.html.twig', ['user' => $resource]);
                $message['text'] = $this->twig->render('email_templates/user/created.txt.twig', ['user' => $resource]);

                $to = [
                    'email' => $resource->getEmail(),
                    'name' => sprintf('%s %s', $resource->getGivenName(), $resource->getFamilyName()),
                ];

                $mjMessage = $this->mailjetHelper->send($to, $subject, $message);

                if ($mjMessage !== null && $mjMessage->success()) {
                    $this->logger->info('Mailjet: confirmation email sent successfully!');
                } else {
                    $this->logger->error('Mailjet: could not send confirmation email.');
                    $this->logger->error(sprintf('Mailjet log: %s', json_encode($mjMessage->getData())));
                }
            } catch (\Exception $exception) {
                $this->logger->error('Mailjet: could not send confirmation email.');
                $this->logger->error(sprintf('%s on file %s on line %s', $exception->getMessage(), $exception->getFile(), $exception->getLine()));
            }
        }
    }
}
