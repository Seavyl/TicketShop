<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types; // Nécessaire pour Types::DECIMAL etc.
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups; // TRÈS IMPORTANT pour les groupes

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')] // Utiliser des backticks si 'order' est un mot réservé SQL
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['order:read', 'order:item:read', 'user:read']]), // Lire la commande, ses items et l'utilisateur
        new GetCollection(normalizationContext: ['groups' => ['order:list']]), // Vue allégée pour la liste
        new Post(denormalizationContext: ['groups' => ['order:write']]),
        new Put(denormalizationContext: ['groups' => ['order:write']]),
        new Patch(denormalizationContext: ['groups' => ['order:write']]),
        new Delete()
    ],
    normalizationContext: ['groups' => ['order:read']], // Contexte de lecture par défaut
    denormalizationContext: ['groups' => ['order:write']], // Contexte d'écriture par défaut
    // paginationItemsPerPage: 10 // Exemple de configuration de la pagination
)]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['order:read', 'order:list'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)] // Pour les montants monétaires
    #[Groups(['order:read', 'order:list', 'order:write'])] // Peut-être calculé, ou défini lors de la création
    private ?string $totalAmount = null; // Stocké comme string pour la précision

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['order:read', 'order:list'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')] // 'User' avec U majuscule
    #[ORM\JoinColumn(nullable: false)] // Une commande doit avoir un utilisateur
    #[Groups(['order:read', 'order:write'])] // Exposer l'utilisateur en lecture, permettre de le lier en écriture (via IRI)
    private ?User $customerUser = null; // Nom de propriété plus explicite

    /**
     * @var Collection<int, OrderItems>
     */
    #[ORM\OneToMany(
        targetEntity: OrderItems::class,
        mappedBy: 'relatedOrder', // DOIT correspondre à la propriété ManyToOne dans OrderItems
        cascade: ['persist', 'remove'], // Gérer la persistance et la suppression des items avec la commande
        orphanRemoval: true
    )]
    #[Groups(['order:read', 'order:item:read', 'order:write'])] // Pour lire les items et potentiellement les gérer en écriture imbriquée
    private Collection $orderItems; // Renommé pour plus de clarté

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable(); // Initialiser la date de création
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): static
    {
        $this->totalAmount = $totalAmount;
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

    public function getCustomerUser(): ?User
    {
        return $this->customerUser;
    }

    public function setCustomerUser(?User $customerUser): static
    {
        $this->customerUser = $customerUser;
        return $this;
    }

    /**
     * @return Collection<int, OrderItems>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItems $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            // Assurez-vous que OrderItems a une méthode setRelatedOrder
            // et que la propriété $relatedOrder dans OrderItems est bien celle qui fait le lien ManyToOne
            $orderItem->setRelatedOrder($this);
        }
        return $this;
    }

    public function removeOrderItem(OrderItems $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            // Assurez-vous que OrderItems a une méthode getRelatedOrder
            if ($orderItem->getRelatedOrder() === $this) {
                $orderItem->setRelatedOrder(null);
            }
        }
        return $this;
    }
}
