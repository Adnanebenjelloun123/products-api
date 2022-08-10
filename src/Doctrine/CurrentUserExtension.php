<?php

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

final class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private $security;
    private $tokenStorage;
    private $userRepository;

    public function __construct(Security $security, TokenStorageInterface $tokenStorage, UserRepository $userRepository)
    {
        $this->security = $security;
        $this->tokenStorage = $tokenStorage;
        $this->userRepository = $userRepository;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }
    
        if ($this->security->isGranted('ROLE_CLIENT')) {
            
            $user = $this->security->getUser();

            $rootAlias = $queryBuilder->getRootAliases()[0];

            if (method_exists($resourceClass, 'getOwner')) {

                $queryBuilder->leftJoin(sprintf('%s.owner', $rootAlias), 'u');
            }

            if (method_exists($resourceClass, 'getUser')) {

                $queryBuilder->leftJoin(sprintf('%s.user', $rootAlias), 'u');
            }


            $queryBuilder->andWhere('u.id = :userId');
            $queryBuilder->setParameter('userId', $user->getId());
            return;
        }
        if ($this->security->isGranted('ROLE_AGENT')) {
            $user = $this->userRepository->findOneBy(['email' => $this->security->getUser()->getUsername()]);
            $rootAlias = $queryBuilder->getRootAliases()[0];

            if (method_exists($resourceClass, 'getOwner')) {
                $queryBuilder->leftJoin(sprintf('%s.owner', $rootAlias), 'u');
            }

            if (method_exists($resourceClass, 'getUser')) {
                $queryBuilder->leftJoin(sprintf('%s.user', $rootAlias), 'u');
            }
            $accountOwnerId = $user->getId();

            if (null !== $user->getParentAccount()) {

                $accountOwnerId = $user->getParentAccount()->getId();
            }
          
            $queryBuilder->andWhere('u.id = :userId');
            $queryBuilder->setParameter('userId', $accountOwnerId);

            return;
        }

        if ($this->security->isGranted('ROLE_AGENT_LEAD_CITY') && (method_exists($resourceClass, 'getCity'))) {
            $user = $this->security->getUser();

            $rootAlias = $queryBuilder->getRootAliases()[0];

            $queryBuilder->leftJoin(sprintf('%s.city', $rootAlias), 'c');
            $queryBuilder->leftJoin('c.users', 'u');
            $queryBuilder->andWhere('u.id = :userId');

            $queryBuilder->setParameter('userId', $user->getId());
        }
    }
}
