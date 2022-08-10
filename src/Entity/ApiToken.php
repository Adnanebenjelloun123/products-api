<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={
 *         "access_control"="is_granted('ROLE_ADMIN')",
 *         "normalization_context"={"groups"={"apiToken", "apiToken:read"}, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"apiToken", "apiToken:write"}},
 *     }
 * )
 * @ORM\Entity(repositoryClass=ApiTokenRepository::class)
 */

class ApiToken
{
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"apiToken:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"apiToken"})
     */
    private $token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups ({"apiToken"})
     */
    private $expiresAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups ({"apiToken"})
     */
    private ?string $hostname;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="apiTokens")
     * @Groups({"apiToken"})
     */
    private $user;

    /**
     * ApiToken constructor.
     *
     * @throws Exception
     */
    public function __construct(User $user = null)
    {
        $this->token = bin2hex(random_bytes(30));
        if (null !== $user) {
            $this->user = $user;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken($token): void
    {
        $this->token = $token;
    }

    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function isExpired(): bool
    {
        if (null === $this->getExpiresAt()) {
            return false;
        }

        return $this->getExpiresAt() <= new DateTime();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function setHostname(?string $hostname): self
    {
        $this->hostname = $hostname;

        return $this;
    }
}