<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\OrderRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity]
#[ORM\Table(name: "orders")]
#[ApiResource(
    normalizationContext: ['groups' => ['order:read']],
    denormalizationContext: ['groups' => ['order:write']],
    operations: [
        new GetCollection(
            security:"is_granted('PUBLIC_ACCESS')"
        ),
        new Get(
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Post(
            security: "is_granted('ROLE_USER')"
        ),
        new Put(
            security: "is_granted('ROLE_ADMIN') or object.getUser() == user"
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or object.getUser() == user"
        )
    ]
)]
class Order
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['order:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision:10, scale:2)]
    #[Groups(['order:read','order:write'])]
    private ?string $totalAmount = null;

    #[ORM\Column]
    #[Groups(['order:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'orderItem')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['order:read','order:write'])]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'relatedOrder', targetEntity: OrderItem::class, cascade: ['persist','remove'])]
    #[Groups(['order:read','order:write'])]
    private Collection $orderItem;

    public function __construct()
    {
        $this->orderItem = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItem(): Collection
    {
        return $this->orderItem;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItem->contains($orderItem)) {
            $this->orderItem->add($orderItem);
            $orderItem->setRelatedOrder($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItem->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getRelatedOrder() === $this) {
                $orderItem->setRelatedOrder(null);
            }
        }

        return $this;
    }
}
