<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
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

    #[ORM\Column(length:255, unique:true)]
    #[Groups(['category:read','category:write'])]
    private ?string $name = null;

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
}
