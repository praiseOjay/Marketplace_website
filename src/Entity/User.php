<?php

namespace App\Entity;

use App\Enum\AdvertStatus;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imageFileName = null;

    #[ORM\OneToMany(mappedBy: 'username', targetEntity: Advert::class, orphanRemoval: true)]
    private Collection $adverts;

    #[ORM\OneToMany(mappedBy: 'buyer', targetEntity: Advert::class)]
    private Collection $boughtAdverts;

    #[ORM\ManyToMany(targetEntity: Advert::class, inversedBy: 'favoritedBy')]
    #[ORM\JoinTable(name: 'user_favorite_adverts')]
    private Collection $favoriteAdverts;

    public function __construct()
    {
        $this->adverts = new ArrayCollection();
        $this->boughtAdverts = new ArrayCollection();
        $this->favoriteAdverts = new ArrayCollection();
    }

    public function getAdverts() : Collection
    {
        return $this->adverts;
    }

    public function addAdvert(Advert $advert) : self
    {
        if (!$this->adverts->contains($advert)) {
            $this->adverts->add($advert);
            $advert->setUser($this);
        }
        return $this;
    }

    public function removeAdvert(Advert $advert) : self
    {
        if ($this->adverts->removeElement($advert)) {
            if ($advert->getUser() === $this) {
                $advert->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Advert>
     */
    public function getBoughtAdverts(): Collection
    {
        return $this->boughtAdverts;
    }

    public function getBoughtAdvertsCount(): int
    {
        return $this->boughtAdverts->count();
    }

    public function getSoldAdvertsCount(): int
    {
        $count = 0;
        foreach ($this->adverts as $advert) {
            if ($advert->getStatus() === AdvertStatus::SOLD) {
                $count++;
            }
        }
        return $count;
    }

    public function getTotalEarnings(): float
    {
        $total = 0.0;
        foreach ($this->adverts as $advert) {
            if ($advert->getStatus() === AdvertStatus::SOLD) {
                $total += (float) $advert->getPrice();
            }
        }
        return $total;
    }

    public function getActiveAdvertsCount(): int
    {
        $count = 0;
        foreach ($this->adverts as $advert) {
            if ($advert->getStatus() === AdvertStatus::PUBLISHED) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * @return Collection<int, Advert>
     */
    public function getFavoriteAdverts(): Collection
    {
        return $this->favoriteAdverts;
    }

    public function addFavoriteAdvert(Advert $advert): static
    {
        if (!$this->favoriteAdverts->contains($advert)) {
            $this->favoriteAdverts->add($advert);
        }

        return $this;
    }

    public function removeFavoriteAdvert(Advert $advert): static
    {
        $this->favoriteAdverts->removeElement($advert);

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getImageFileName(): ?string
    {
        return $this->imageFileName;
    }

    public function setImageFileName(?string $imageFileName): self
    {
        $this->imageFileName = $imageFileName;

        return $this;
    }
}
