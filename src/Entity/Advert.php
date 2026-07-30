<?php

namespace App\Entity;

use App\Enum\AdvertStatus;
use App\Repository\AdvertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: AdvertRepository::class)]
#[ORM\Index(name: 'idx_advert_slug', columns: ['slug'])]
#[ORM\Index(name: 'idx_advert_status', columns: ['status'])]
#[ORM\Index(name: 'idx_advert_published', columns: ['is_published'])]
class Advert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $time_stamp = null;

    #[ORM\ManyToOne(targetEntity: Categories::class, inversedBy: 'adverts')]
    private $category;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'adverts')]
    private $username;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'boughtAdverts')]
    private ?User $buyer = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imageFileName = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $isPublished = true;

    #[ORM\Column(type: 'string', enumType: AdvertStatus::class)]
    private AdvertStatus $status = AdvertStatus::PUBLISHED;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favoriteAdverts')]
    private Collection $favoritedBy;

    public function __construct()
    {
        $this->favoritedBy = new ArrayCollection();
        $this->status = AdvertStatus::PUBLISHED;
        $this->isPublished = true;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): self
    {
        $this->category = $category;
        return $this;
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

    public function getUser(): ?User
    {
        return $this->username;
    }

    public function setUser(?User $user): self
    {
        $this->username = $user;
        return $this;
    }

    public function getBuyer(): ?User
    {
        return $this->buyer;
    }

    public function setBuyer(?User $buyer): static
    {
        $this->buyer = $buyer;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        $this->generateSlug();

        return $this;
    }

    public function getSlug(): ?string
    {
        if (!$this->slug && $this->title) {
            $this->generateSlug();
        }
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function generateSlug(): static
    {
        if ($this->title) {
            $slugger = new AsciiSlugger();
            $this->slug = strtolower($slugger->slug($this->title)->toString());
        }
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTimeStamp(): ?\DateTimeInterface
    {
        return $this->time_stamp;
    }

    public function setTimeStamp(?\DateTimeInterface $time_stamp): static
    {
        $this->time_stamp = $time_stamp;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(?bool $isPublished): void
    {
        $this->isPublished = $isPublished;
    }

    public function getStatus(): AdvertStatus
    {
        return $this->status;
    }

    public function setStatus(AdvertStatus $status): static
    {
        $this->status = $status;
        $this->isPublished = ($status === AdvertStatus::PUBLISHED);
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getFavoritedBy(): Collection
    {
        return $this->favoritedBy;
    }

    public function addFavoritedBy(User $user): static
    {
        if (!$this->favoritedBy->contains($user)) {
            $this->favoritedBy->add($user);
            $user->addFavoriteAdvert($this);
        }

        return $this;
    }

    public function removeFavoritedBy(User $user): static
    {
        if ($this->favoritedBy->removeElement($user)) {
            $user->removeFavoriteAdvert($this);
        }

        return $this;
    }

    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->favoritedBy->contains($user);
    }
}
