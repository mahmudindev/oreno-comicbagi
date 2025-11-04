<?php

namespace App\Entity;

use App\Repository\ComicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ComicRepository::class)]
#[ORM\Table(name: 'comic')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
class Comic
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    #[Serializer\Groups(['comic'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
    #[Serializer\Groups(['comic'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 12, unique: true, options: ['collation' => 'utf8mb4_bin'])]
    #[Assert\NotBlank, Assert\Length(12)]
    #[Serializer\Groups(['comic'])]
    private ?string $code = null;

    /**
     * @var Collection<int, ComicProvider>
     */
    #[ORM\OneToMany(targetEntity: ComicProvider::class, mappedBy: 'comic', fetch: 'EXTRA_LAZY')]
    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
    private Collection $providers;

    /**
     * @var Collection<int, ComicChapter>
     */
    #[ORM\OneToMany(targetEntity: ComicChapter::class, mappedBy: 'comic', fetch: 'EXTRA_LAZY')]
    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
    private Collection $chapters;

    public function __construct()
    {
        $this->providers = new ArrayCollection();
        $this->chapters = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(PrePersistEventArgs $args)
    {
        $this->setCreatedAt(new \DateTimeImmutable());
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(PreUpdateEventArgs $args)
    {
        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, ComicProvider>
     */
    public function getProviders(): Collection
    {
        return $this->providers;
    }

    #[Serializer\Groups(['comic'])]
    public function getProviderCount(): ?int
    {
        return $this->providers->count();
    }

    public function addProvider(ComicProvider $provider): static
    {
        if (!$this->providers->contains($provider)) {
            $this->providers->add($provider);
            $provider->setComic($this);
        }

        return $this;
    }

    public function removeProvider(ComicProvider $provider): static
    {
        if ($this->providers->removeElement($provider)) {
            // set the owning side to null (unless already changed)
            if ($provider->getComic() === $this) {
                $provider->setComic(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ComicChapter>
     */
    public function getChapters(): Collection
    {
        return $this->chapters;
    }

    #[Serializer\Groups(['comic'])]
    public function getChapterCount(): ?int
    {
        return $this->chapters->count();
    }

    public function addChapter(ComicChapter $chapter): static
    {
        if (!$this->chapters->contains($chapter)) {
            $this->chapters->add($chapter);
            $chapter->setComic($this);
        }

        return $this;
    }

    public function removeChapter(ComicChapter $chapter): static
    {
        if ($this->chapters->removeElement($chapter)) {
            // set the owning side to null (unless already changed)
            if ($chapter->getComic() === $this) {
                $chapter->setComic(null);
            }
        }

        return $this;
    }
}
