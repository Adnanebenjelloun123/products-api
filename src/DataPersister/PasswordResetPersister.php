<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Custom\PasswordReset;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordResetPersister implements DataPersisterInterface
{
    private EntityManagerInterface $entityManager;
    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function supports($data): bool
    {
        return $data instanceof PasswordReset;
    }

    /**
     * @param PasswordReset $data
     */
    public function persist($data)
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneByConfirmationToken($data->token);

        if (null !== $user && $data->password === $data
                ->passwordRepeated) {

            $user->setPassword(
                $this->userPasswordEncoder->encodePassword($user, $data->password)
            );

            $user->setConfirmationToken(null);

            $user->eraseCredentials();

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    public function remove($data)
    {
        throw new \Exception('Not supported!');
    }
}