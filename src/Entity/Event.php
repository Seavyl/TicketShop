<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups; // TRÈS IMPORTANT
use App\Repository\EventRepository; // Assurez-vous que le nom du repository est correct

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['event:read']]), // Groupe spécifique à Contact
        new GetCollection(normalizationContext: ['groups' => ['event:read']]),
        new Post(denormalizationContext: ['groups' => ['event:write']]),
        new Put(denormalizationContext: ['groups' => ['event:write']]) ,  // Opération PUT
        new Delete(),
    ],
    normalizationContext: ['groups' => ['event:read']], // Groupe par défaut pour la lecture
    denormalizationContext: ['groups' => ['event:write']] // Groupe par défaut pour l'écriture
)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event:read'])] // L'ID est généralement en lecture seule
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['event:read', 'event:write'])] // Permettre la lecture et l'écriture pour title
    #[NotBlank(groups: ['event:write'])]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)] // Si artist_name peut être null
    #[Groups(['event:read', 'event:write'])]
    private ?string $artistName = null; // Propriété en camelCase

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['event:read', 'event:write'])]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['event:read', 'event:write'])]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255)]
    #[Groups(['event:read', 'event:write'])]
    private ?string $venue = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
#[Groups(['event:read', 'event:write'])]
#[NotBlank(groups: ['event:write'])] // Obligatoire à l'écriture
#[PositiveOrZero(message: "Le prix doit être positif ou nul.", groups: ['event:write'])]
private ?string $price = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['event:read', 'event:write'])]
    private ?int $remainingQuantity = null;

    // --- Getters et Setters ---
    // (Assurez-vous qu'ils sont tous présents)

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getArtistName(): ?string
    {
        return $this->artistName;
    }

    public function setArtistName(?string $artistName): static
    {
        $this->artistName = $artistName;
        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
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
}
