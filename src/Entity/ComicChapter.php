<?php

namespace App\Entity;

use App\Repository\ComicChapterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ComicChapterRepository::class)]
#[ORM\Table(name: 'comic_chapter')]
#[ORM\UniqueConstraint(columns: ['comic_id', 'number', 'version'])]
#[ORM\HasLifecycleCallbacks]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
class ComicChapter
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    #[Serializer\Groups(['comic', 'comicChapter'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
    #[Serializer\Groups(['comic', 'comicChapter'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'chapters')]
    #[ORM\JoinColumn(name: 'comic_id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?Comic $comic = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Serializer\Groups(['comic', 'comicChapter'])]
    private ?string $number = null;

    #[ORM\Column(length: 64, options: ['default' => ''])]
    #[Assert\Length(min: 1, max: 64)]
    #[Serializer\Groups(['comic', 'comicChapter'])]
    private ?string $version = null;

    /**
     * @var Collection<int, ComicChapterProvider>
     */
    #[ORM\OneToMany(targetEntity: ComicChapterProvider::class, mappedBy: 'chapter')]
    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
    private Collection $providers;

    public function __construct()
    {
        $this->providers = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(PrePersistEventArgs $args)
    {
        $this->setCreatedAt(new \DateTimeImmutable());

        if ($this->version == null) $this->setVersion('');
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(PreUpdateEventArgs $args)
    {
        $this->setUpdatedAt(new \DateTimeImmutable());

        if ($this->version == null) $this->setVersion('');
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

    public function getComic(): ?Comic
    {
        return $this->comic;
    }

    #[Serializer\Groups(['comicChapter'])]
    public function getComicCode(): ?string
    {
        if ($this->comic == null) {
            return null;
        }

        return $this->comic->getCode();
    }

    public function setComic(?Comic $comic): static
    {
        $this->comic = $comic;

        return $this;
    }

    public function getNumber(): ?float
    {
        return $this->number;
    }

    public function setNumber(float $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): static
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return Collection<int, ComicChapterProvider>
     */
    public function getProviders(): Collection
    {
        return $this->providers;
    }

    #[Serializer\Groups(['comic', 'comicChapter'])]
    public function getProviderCount(): ?int
    {
        return $this->providers->count();
    }

    public function addProvider(ComicChapterProvider $provider): static
    {
        if (!$this->providers->contains($provider)) {
            $this->providers->add($provider);
            $provider->setChapter($this);
        }

        return $this;
    }

    public function removeProvider(ComicChapterProvider $provider): static
    {
        if ($this->providers->removeElement($provider)) {
            // set the owning side to null (unless already changed)
            if ($provider->getChapter() === $this) {
                $provider->setChapter(null);
            }
        }

        return $this;
    }
}
