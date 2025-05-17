<?php

namespace App\Entity;

use ApiPlatform\Metadata\Put;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ContactRepository;
use ApiPlatform\Metadata\Get;             // <-- AJOUTER
use ApiPlatform\Metadata\GetCollection;    // <-- AJOUTER
use ApiPlatform\Metadata\Post;             // <-- AJOUTER
use Symfony\Component\Serializer\Annotation\Groups; // <-- AJOUTER pour les groupes

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['contact:read']]), // Groupe spécifique à Contact
        new GetCollection(normalizationContext: ['groups' => ['contact:read']]),
        new Post(denormalizationContext: ['groups' => ['contact:write']]),
        new Put(denormalizationContext: ['groups' => ['contact:write']]) ,  // Opération PUT
        new Delete(),
    ],
    normalizationContext: ['groups' => ['contact:read']], // Groupe par défaut pour la lecture
    denormalizationContext: ['groups' => ['contact:write']] // Groupe par défaut pour l'écriture
)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['contact:read'])] // Exposer l'ID en lecture
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['contact:read', 'contact:write'])] // Exposer le nom en lecture et écriture
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['contact:read', 'contact:write'])] // Exposer l'email en lecture et écriture
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['contact:read', 'contact:write'])] // Exposer le message en lecture et écriture
    private ?string $message = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }
}
