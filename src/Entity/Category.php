<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CategoryRepository;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    // Contexte de (dé)sérialisation global
    normalizationContext: ['groups' => ['category:read']],
    denormalizationContext: ['groups' => ['category:write']],
    operations: [
        new GetCollection(),                                   // accessible à tous
        new Get(),                                             // accessible à tous
        new Post(security: "is_granted('ROLE_ADMIN')"),        // seuls les ADMIN
        new Put(security: "is_granted('ROLE_ADMIN')"),         
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ]
)]
class Category
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['category:read'])]
    private ?int $id = null;

    // Dans Category.php
    #[ORM\Column(length:255, unique:true)]
    #[Groups(['category:read', 'category:write', 'ticket:read'])] // Ajout de 'ticket:read'
    private ?string $name = null;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'category')]
    private Collection $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setCategory($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getCategory() === $this) {
                $ticket->setCategory(null);
            }
        }

        return $this;
    }
    // AJOUTEZ CETTE MÉTHODE CI-DESSOUS
    /**
     * Représentation textuelle de l'objet Category.
     * Utilisée par EasyAdmin et d'autres parties de Symfony pour afficher l'entité.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name ?? 'Catégorie non définie'; // Retourne la propriété 'name'
    }
}
