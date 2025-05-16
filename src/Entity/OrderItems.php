<?php

namespace App\Entity;

use App\Repository\OrderItemsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemsRepository::class)]
class OrderItems
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?order $event = null;

    #[ORM\ManyToOne(inversedBy: 'quantity')]
    #[ORM\JoinColumn(nullable: false)]
    private ?orderitems $items = null;

    #[ORM\Column]
    private ?int $unit_price = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $create_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): ?order
    {
        return $this->event;
    }

    public function setEvent(?order $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getItems(): ?orderitems
    {
        return $this->items;
    }

    public function setItems(?orderitems $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function getUnitPrice(): ?int
    {
        return $this->unit_price;
    }

    public function setUnitPrice(int $unit_price): static
    {
        $this->unit_price = $unit_price;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->create_at;
    }

    public function setCreateAt(\DateTimeImmutable $create_at): static
    {
        $this->create_at = $create_at;

        return $this;
    }
}
