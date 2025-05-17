<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\OrderItemsRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use ApiPlatform\Metadata\ApiResource; // À ajouter si vous exposez OrderItems via API

#[ORM\Entity(repositoryClass: OrderItemsRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['order_item:read']]), // Groupe spécifique à Contact
        new GetCollection(normalizationContext: ['groups' => ['order_item:read']]),
        new Post(denormalizationContext: ['groups' => ['order_item:write']]),
        new Put(denormalizationContext: ['groups' => ['order_item:write']]) ,  // Opération PUT
        new Delete(),
    ],
    normalizationContext: ['groups' => ['order_item:read']], // Groupe par défaut pour la lecture
    denormalizationContext: ['groups' => ['order_item:write']] // Groupe par défaut pour l'écriture
)]
class OrderItems
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['order_item:read', 'order:read'])] // Visible quand on lit un OrderItem ou une Order
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderItems')] // 'orderItems' est la collection dans Order
    #[ORM\JoinColumn(nullable: false)]
    // Pas besoin de groupe d'écriture ici si les OrderItems sont gérés via la cascade depuis Order
    // Mais un groupe de lecture est utile
    #[Groups(['order_item:read','order_item:write'])]
    private ?Order $relatedOrder = null; // Propriété liant à la comm_item
    #[ORM\ManyToOne(targetEntity: Event::class)] // Supposant que vous avez une entité Event
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['order_item:read', 'order:read', 'order_item:write'])] // Pour lire l'event et le lier en écriture
    private ?Event $concernedEvent = null; // Propriété liant à l'événement/spectacle

    #[ORM\Column(type: Types::INTEGER)] // Quantité est généralement un entier
    #[Groups(['order_item:read', 'order:read', 'order_item:write'])]
    private ?int $quantity = null;

     #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['order_item:read','order_item:write'])]
    #[NotBlank(groups: ['order_item:write'])]
    #[PositiveOrZero(groups: ['order_item:write'])]
    private ?string $unitPrice = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['order_item:read', 'order:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getConcernedEvent(): ?Event
    {
        return $this->concernedEvent;
    }

    public function setConcernedEvent(?Event $concernedEvent): static
    {
        $this->concernedEvent = $concernedEvent;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int|float|string $quantity): static
{
    $this->quantity = (int) $quantity;
    return $this;
}


    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(int|float|string $unitPrice): static
{
    // Force la conversion en string pour le type DECIMAL en base
    $this->unitPrice = (string) $unitPrice;
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
}
