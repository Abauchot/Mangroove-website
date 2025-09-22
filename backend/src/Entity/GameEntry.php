<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\GameEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    normalizationContext: ['groups' => ['game_entry:read']],
    denormalizationContext: ['groups' => ['game_entry:write']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')"
)]
#[ORM\Entity(repositoryClass: GameEntryRepository::class)]
#[ORM\Table(name: '`game_entry`')]
#[ORM\HasLifecycleCallbacks]
class GameEntry
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['game_entry:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['game_entry:read', 'game_entry:write'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['game_entry:read', 'game_entry:write'])]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Jam::class, inversedBy: 'gameEntries')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['game_entry:read', 'game_entry:write'])]
    private ?Jam $jam = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'gameEntries')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['game_entry:read', 'game_entry:write'])]
    private ?User $author = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['game_entry:read', 'game_entry:write'])]
    private ?string $teamName = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['game_entry:read', 'game_entry:write'])]
    private ?array $mediaUrls = [];

    #[ORM\Column(length: 500)]
    #[Groups(['game_entry:read', 'game_entry:write'])]
    private ?string $playUrl = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['game_entry:read', 'game_entry:write'])]
    private ?array $tags = [];

    #[ORM\Column]
    #[Groups(['game_entry:read', 'game_entry:write'])]
    private ?bool $isPublic = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['game_entry:read'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['game_entry:read'])]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'gameEntry')]
    private Collection $comments;

    /**
     * @var Collection<int, Vote>
     */
    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: 'GameEntry')]
    private Collection $votes;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->mediaUrls = [];
        $this->tags = [];
        $this->comments = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?Uuid
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getJam(): ?Jam
    {
        return $this->jam;
    }

    public function setJam(?Jam $jam): static
    {
        $this->jam = $jam;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getTeamName(): ?string
    {
        return $this->teamName;
    }

    public function setTeamName(?string $teamName): static
    {
        $this->teamName = $teamName;

        return $this;
    }

    public function getMediaUrls(): array
    {
        return $this->mediaUrls ?? [];
    }

    public function setMediaUrls(?array $mediaUrls): static
    {
        $this->mediaUrls = $mediaUrls;

        return $this;
    }

    public function addMediaUrl(string $url): static
    {
        if (!in_array($url, $this->mediaUrls ?? [])) {
            $this->mediaUrls[] = $url;
        }

        return $this;
    }

    public function removeMediaUrl(string $url): static
    {
        $this->mediaUrls = array_values(array_filter($this->mediaUrls ?? [], fn($u) => $u !== $url));

        return $this;
    }

    public function getPlayUrl(): ?string
    {
        return $this->playUrl;
    }

    public function setPlayUrl(string $playUrl): static
    {
        $this->playUrl = $playUrl;

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags ?? [];
    }

    public function setTags(?array $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function addTag(string $tag): static
    {
        if (!in_array($tag, $this->tags ?? [])) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(string $tag): static
    {
        $this->tags = array_values(array_filter($this->tags ?? [], fn($t) => $t !== $tag));

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setGameEntry($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getGameEntry() === $this) {
                $comment->setGameEntry(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Vote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): static
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setGameEntry($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): static
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getGameEntry() === $this) {
                $vote->setGameEntry(null);
            }
        }

        return $this;
    }
}
