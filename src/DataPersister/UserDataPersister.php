<?php


namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserDataPersister implements DataPersisterInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->logger = $logger;
    }

    /**
     * Is the data supported by the persister?
     */
    public function supports($data): bool
    {
        // TODO: Implement supports() method.
        return $data instanceof User;
    }

    /**
     * @param User $data
     * @return object|void
     */
    public function persist($data)
    {
        $this->logger->info('user password is '. $data->getPlainPassword());

        if ($data->getPlainPassword()) {
            $data->setPassword(
                $this->userPasswordEncoder->encodePassword($data, $data->getPlainPassword())
            );

            $data->eraseCredentials();
        }

        if (null === $data->getId()) {
            $data->setConfirmationToken(bin2hex($data->getGivenName().$data->getFamilyName().$data->getEmail()));
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @param $data
     */
    public function remove($data)
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}