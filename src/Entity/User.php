<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['user:read']]),
        new GetCollection(normalizationContext: ['groups' => ['user:list']]),
        new Post(denormalizationContext: ['groups' => ['user:write']]),
        new Put(denormalizationContext: ['groups' => ['user:write']]),
        new Patch(denormalizationContext: ['groups' => ['user:write']]),
        new Delete()
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
    // security: "is_granted('ROLE_ADMIN')" // si vous voulez restreindre CRUD User
)]
class User
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['user:read','user:list','order:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read','user:list','user:write'])]
    #[Assert\NotBlank(groups: ['user:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read','user:write'])]
    #[Assert\NotBlank(groups: ['user:write']), Assert\Email(groups: ['user:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:write'])] // password jamais en lecture
    #[Assert\NotBlank(groups: ['user:write']), Assert\Length(min: 6, groups: ['user:write'])]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read','user:write'])]
    private ?string $address = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['user:read','user:write'])]
    private array $role = [];

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(mappedBy: 'customerUser', targetEntity: Order::class)]
    #[Groups(['user:read'])]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    // --- Getters & Setters ---

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { 
        // vous devez hasher ici via UserPasswordHasherInterface dans votre contrôleur/service
        $this->password = $password; 
        return $this; 
    }

    public function getAddress(): ?string { return $this->address; }
    public function setAddress(string $address): static { $this->address = $address; return $this; }

    public function getRole(): array { return $this->role; }
    public function setRole(array $role): static { $this->role = $role; return $this; }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection { return $this->orders; }

    public function addOrder(Order $order): static {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setCustomerUser($this);
        }
        return $this;
    }

    public function removeOrder(Order $order): static {
        if ($this->orders->removeElement($order) && $order->getCustomerUser() === $this) {
            $order->setCustomerUser(null);
        }
        return $this;
    }
}
