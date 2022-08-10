<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SupportTicketRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiResource(
 *     normalizationContext={"groups" = {"support_ticket:read"}},
 *     denormalizationContext={"groups" = {"support_ticket:write"}},
 *     collectionOperations={
 *          "get",
 *          "post"
 *     },
 *     itemOperations={
 *          "get" = {"normalization_context"={"groups"={"support_ticket:read", "support_ticket:item:read"}}},
 *          "put",
 *          "patch",
 *          "delete",
 *     }
 * )
 * @ORM\Entity(repositoryClass=SupportTicketRepository::class)
 * @ApiFilter(SearchFilter::class, properties={"email":"start", "subject":"partial"})
 */
class SupportTicket
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"support_ticket:read", "support_ticket:write"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"support_ticket:read", "support_ticket:write"})
     */
    private $subject;

    /**
     * @ORM\Column(type="text")
     * @Groups ({"support_ticket:item:read", "support_ticket:write"})
     */
    private $message;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
