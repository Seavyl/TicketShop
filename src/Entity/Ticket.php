<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TicketRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity]
#[ApiResource(
    normalizationContext: ['groups' => ['ticket:read']],
    denormalizationContext: ['groups' => ['ticket:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: "is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ]
)]
class Ticket
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['ticket:read'])]
    private ?int $id = null;

    #[ORM\Column(length:255)]
    #[Groups(['ticket:read','ticket:write'])]
    private ?string $artistName = null;

    #[ORM\Column]
    #[Groups(['ticket:read','ticket:write'])]
    private ?\DateTime $startDate = null;

    #[ORM\Column]
    #[Groups(['ticket:read','ticket:write'])]
    private ?\DateTime $endDate = null;

    #[ORM\Column(length:255)]
    #[Groups(['ticket:read','ticket:write'])]
    private ?string $venue = null;

    #[ORM\Column(type: Types::DECIMAL, precision:10, scale:2)]
    #[Groups(['ticket:read','ticket:write'])]
    private ?string $price = null;

    #[ORM\Column]
    #[Groups(['ticket:read','ticket:write'])]
    private ?int $remainingQuantity = null;

    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: OrderItem::class)]
    #[Groups(['ticket:read'])]
    private Collection $orderItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArtistName(): ?string
    {
        return $this->artistName;
    }

    public function setArtistName(string $artistName): static
    {
        $this->artistName = $artistName;

        return $this;
    }

    public function getstartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setstartDate(\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getVenue(): ?string
    {
        return $this->venue;
    }

    public function setVenue(string $venue): static
    {
        $this->venue = $venue;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getRemainingQuantity(): ?int
    {
        return $this->remainingQuantity;
    }

    public function setRemainingQuantity(int $remainingQuantity): static
    {
        $this->remainingQuantity = $remainingQuantity;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setTicket($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getTicket() === $this) {
                $orderItem->setTicket(null);
            }
        }

        return $this;
    }
}
