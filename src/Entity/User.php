<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    normalizationContext: ['groups' => ['ticket:read']],
    denormalizationContext: ['groups' => ['ticket:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: "is_granted('ROLE_ADMIN')"),
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
        $this->user = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(Order $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
            $user->setUser($this);
        }

        return $this;
    }

    public function removeUser(Order $user): static
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getUser() === $this) {
                $user->setUser(null);
            }
        }

        return $this;
    }
}
