<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Custom\PasswordReset;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class PasswordResetDataProvider implements ContextAwareCollectionDataProviderInterface, ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === PasswordReset::class;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        return [];
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneByConfirmationToken($id);

        if (null === $user) {
            return [];
        }

        $passwordReset = new PasswordReset();

        $passwordReset->token = $id;
        $passwordReset->password = '';
        $passwordReset->passwordRepeated = '';

        return $passwordReset;
    }
}