<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post as ApiPost;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\ForumThreadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('IS_AUTHENTICATED_FULLY')"),
        new Get(security: "is_granted('IS_AUTHENTICATED_FULLY')"),
        new ApiPost(security: "is_granted('IS_AUTHENTICATED_FULLY')", denormalizationContext: ['groups' => ['forum_thread:create']]),
        new Patch(security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_MODERATOR') and object.getAuthor() == user) or object.getAuthor() == user"),
        new Delete(security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_MODERATOR') and object.getAuthor() == user)")
    ],
    normalizationContext: ['groups' => ['forum_thread:read']],
    denormalizationContext: ['groups' => ['forum_thread:write']]
)]
#[ORM\Entity(repositoryClass: ForumThreadRepository::class)]
#[ORM\Table(name: 'forum_thread')]
#[ORM\HasLifecycleCallbacks]
class ForumThread
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['forum_thread:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[Groups(['forum_thread:read', 'forum_thread:write', 'forum_thread:create'])]
    private ?string $title = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'forumThreads')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['forum_thread:read', 'forum_thread:create'])]
    private ?User $author = null;

    #[ORM\ManyToOne(targetEntity: Jam::class, inversedBy: 'forumThreads')]
    #[Groups(['forum_thread:read', 'forum_thread:write', 'forum_thread:create'])]
    private ?Jam $jam = null;

    #[ORM\Column]
    #[Groups(['forum_thread:read', 'forum_thread:write'])]
    private ?bool $isPublic = true;

    #[ORM\Column]
    #[Groups(['forum_thread:read'])]
    private ?bool $isAnnouncement = false;

    #[ORM\Column]
    #[Groups(['forum_thread:read'])]
    private ?bool $pinned = false;

    #[ORM\Column]
    #[Groups(['forum_thread:read'])]
    private ?bool $locked = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['forum_thread:read'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['forum_thread:read'])]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, ForumPost>
     */
    #[ORM\OneToMany(targetEntity: ForumPost::class, mappedBy: 'thread', cascade: ['remove'])]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    #[Groups(['forum_thread:read'])]
    private Collection $forumPosts;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->forumPosts = new ArrayCollection();
        $this->isPublic = true;
        $this->isAnnouncement = false;
        $this->pinned = false;
        $this->locked = false;
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;
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

    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    public function getIsAnnouncement(): ?bool
    {
        return $this->isAnnouncement;
    }

    public function setIsAnnouncement(bool $isAnnouncement): static
    {
        $this->isAnnouncement = $isAnnouncement;
        return $this;
    }

    public function getPinned(): ?bool
    {
        return $this->pinned;
    }

    public function setPinned(bool $pinned): static
    {
        $this->pinned = $pinned;
        return $this;
    }

    public function getLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): static
    {
        $this->locked = $locked;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, ForumPost>
     */
    public function getForumPosts(): Collection
    {
        return $this->forumPosts;
    }

    public function addForumPost(ForumPost $forumPost): static
    {
        if (!$this->forumPosts->contains($forumPost)) {
            $this->forumPosts->add($forumPost);
            $forumPost->setThread($this);
        }
        return $this;
    }

    public function removeForumPost(ForumPost $forumPost): static
    {
        if ($this->forumPosts->removeElement($forumPost)) {
            if ($forumPost->getThread() === $this) {
                $forumPost->setThread(null);
            }
        }
        return $this;
    }

    public function setPostsCount(int $postsCount): static
    {
        $this->postsCount = $postsCount;
        return $this;
    }

    public function setLastActivityAt(\DateTimeInterface $lastActivityAt): static
    {
        $this->lastActivityAt = $lastActivityAt;
        return $this;
    }
}