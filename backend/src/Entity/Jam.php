<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\JamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    normalizationContext: ['groups' => ['jam:read']],
    denormalizationContext: ['groups' => ['jam:write']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')"
)]

#[ORM\Entity(repositoryClass: JamRepository::class)]
#[ORM\Table(name: '`jam`')]
#[ORM\HasLifecycleCallbacks]
class Jam
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_RUNNING = 'running';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_ARCHIVED = 'archived';


    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['jam:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['jam:read', 'jam:write'])]
    private ?string $title = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['jam:read', 'jam:write'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['jam:read', 'jam:write'])]
    private ?\DateTimeInterface $startsAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['jam:read', 'jam:write'])]
    private ?\DateTimeInterface $endsAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['jam:read', 'jam:write'])]
    private ?\DateTimeInterface $votingEndAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['jam:read', 'jam:write'])]
    private ?\DateTimeInterface $themeSubmissionEndAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['jam:read', 'jam:write'])]
    private ?\DateTimeInterface $themeVotingEndAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['jam:read', 'jam:write'])]
    private ?string $theme = null;

    #[ORM\Column(length: 50)]
    #[Groups(['jam:read', 'jam:write'])]
    private ?string $status = 'draft';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['jam:read'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['jam:read'])]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(targetEntity: GameEntry::class, mappedBy: 'jam', orphanRemoval: true)]
    #[Groups(['jam:read'])]
    private Collection $gameEntries;

    /**
     * @var Collection<int, ThemeProposal>
     */
    #[ORM\OneToMany(targetEntity: ThemeProposal::class, mappedBy: 'jam')]
    private Collection $themeProposals;

    /**
     * @var Collection<int, ForumThread>
     */
    #[ORM\OneToMany(targetEntity: ForumThread::class, mappedBy: 'jam')]
    private Collection $forumThreads;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->status = self::STATUS_DRAFT;
        $this->gameEntries = new ArrayCollection();
        $this->themeProposals = new ArrayCollection();
        $this->forumThreads = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getStartsAt(): ?\DateTimeInterface
    {
        return $this->startsAt;
    }

    public function setStartsAt(\DateTimeInterface $startsAt): static
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getEndsAt(): ?\DateTimeInterface
    {
        return $this->endsAt;
    }

    public function setEndsAt(\DateTimeInterface $endsAt): static
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    public function getVotingEndAt(): ?\DateTimeInterface
    {
        return $this->votingEndAt;
    }

    public function setVotingEndAt(\DateTimeInterface $votingEndAt): static
    {
        $this->votingEndAt = $votingEndAt;

        return $this;
    }

    public function getThemeSubmissionEndAt(): ?\DateTimeInterface
    {
        return $this->themeSubmissionEndAt;
    }

    public function setThemeSubmissionEndAt(\DateTimeInterface $themeSubmissionEndAt): static
    {
        $this->themeSubmissionEndAt = $themeSubmissionEndAt;

        return $this;
    }

    public function getThemeVotingEndAt(): ?\DateTimeInterface
    {
        return $this->themeVotingEndAt;
    }

    public function setThemeVotingEndAt(\DateTimeInterface $themeVotingEndAt): static
    {
        $this->themeVotingEndAt = $themeVotingEndAt;

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $allowedStatuses = [
            self::STATUS_DRAFT,
            self::STATUS_PUBLISHED,
            self::STATUS_RUNNING,
            self::STATUS_CLOSED,
            self::STATUS_ARCHIVED,
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid status "%s"', $status));
        }

        $this->status = $status;
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
     * @return Collection<int, GameEntry>
     */
    public function getGameEntries(): Collection
    {
        return $this->gameEntries;
    }

    public function addGameEntry(GameEntry $gameEntry): static
    {
        if (!$this->gameEntries->contains($gameEntry)) {
            $this->gameEntries->add($gameEntry);
            $gameEntry->setJam($this);
        }

        return $this;
    }

    public function removeGameEntry(GameEntry $gameEntry): static
    {
        if ($this->gameEntries->removeElement($gameEntry)) {
            // set the owning side to null (unless already changed)
            if ($gameEntry->getJam() === $this) {
                $gameEntry->setJam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ThemeProposal>
     */
    public function getThemeProposals(): Collection
    {
        return $this->themeProposals;
    }

    public function addThemeProposal(ThemeProposal $themeProposal): static
    {
        if (!$this->themeProposals->contains($themeProposal)) {
            $this->themeProposals->add($themeProposal);
            $themeProposal->setJamId($this);
        }

        return $this;
    }

    public function removeThemeProposal(ThemeProposal $themeProposal): static
    {
        if ($this->themeProposals->removeElement($themeProposal)) {
            // set the owning side to null (unless already changed)
            if ($themeProposal->getJamId() === $this) {
                $themeProposal->setJamId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ForumThread>
     */
    public function getForumThreads(): Collection
    {
        return $this->forumThreads;
    }

    public function addForumThread(ForumThread $forumThread): static
    {
        if (!$this->forumThreads->contains($forumThread)) {
            $this->forumThreads->add($forumThread);
            $forumThread->setJam($this);
        }

        return $this;
    }

    public function removeForumThread(ForumThread $forumThread): static
    {
        if ($this->forumThreads->removeElement($forumThread)) {
            if ($forumThread->getJam() === $this) {
                $forumThread->setJam(null);
            }
        }

        return $this;
    }
}
