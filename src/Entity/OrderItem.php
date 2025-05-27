<?php

namespace App\Entity;


use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\OrderItemRepository;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    normalizationContext: ['groups' => ['orderItem:read']],
    denormalizationContext: ['groups' => ['orderItem:write']],
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Get(
            security: "is_granted('ROLE_ADMIN') or object.getRelatedOrder().getUser() == user"
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN') or object.getRelatedOrder().getUser() == user"
        ),
        new Put(
            security: "is_granted('ROLE_ADMIN') or object.getRelatedOrder().getUser() == user"
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or object.getRelatedOrder().getUser() == user"
        )
    ]
)]
class OrderItem
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['orderItem:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['orderItem:read','orderItem:write'])]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision:10, scale:2)]
    #[Groups(['orderItem:read','orderItem:write'])]
    private ?string $unitPrice = null;

    #[ORM\Column]
    #[Groups(['orderItem:read','orderItem:write'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'orderItem')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['orderItem:read','orderItem:write'])]
    private ?Order $relatedOrder = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['orderItem:read','orderItem:write'])]
    private ?Ticket $ticket = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(string $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRelatedOrder(): ?Order
    {
        return $this->relatedOrder;
    }

    public function setRelatedOrder(?Order $relatedOrder): static
    {
        $this->relatedOrder = $relatedOrder;

        return $this;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): static
    {
        $this->ticket = $ticket;

        return $this;
    }
}
