<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post as ApiPost;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\ForumPostRepository;
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
        new ApiPost(security: "is_granted('IS_AUTHENTICATED_FULLY')", denormalizationContext: ['groups' => ['forum_post:create']]),
        new Patch(security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_MODERATOR') and object.getAuthor() == user) or object.getAuthor() == user"),
        new Delete(security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_MODERATOR') and object.getAuthor() == user) or object.getAuthor() == user")
    ],
    normalizationContext: ['groups' => ['forum_post:read']],
    denormalizationContext: ['groups' => ['forum_post:write']]
)]
#[ORM\Entity(repositoryClass: ForumPostRepository::class)]
#[ORM\Table(name: 'forum_post')]
#[ORM\HasLifecycleCallbacks]
class ForumPost
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['forum_post:read'])]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: ForumThread::class, inversedBy: 'forumPosts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['forum_post:read', 'forum_post:create'])]
    private ?ForumThread $thread = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'forumPosts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['forum_post:read', 'forum_post:create'])]
    private ?User $author = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 10000)]
    #[Groups(['forum_post:read', 'forum_post:write', 'forum_post:create'])]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: ForumPost::class, inversedBy: 'replies')]
    #[Groups(['forum_post:read', 'forum_post:write', 'forum_post:create'])]
    private ?ForumPost $parent = null;

    /**
     * @var Collection<int, ForumPost>
     */
    #[ORM\OneToMany(targetEntity: ForumPost::class, mappedBy: 'parent', cascade: ['remove'])]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    #[Groups(['forum_post:read'])]
    private Collection $replies;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['forum_post:read'])]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->replies = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getThread(): ?ForumThread
    {
        return $this->thread;
    }

    public function setThread(?ForumThread $thread): static
    {
        $this->thread = $thread;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getParent(): ?ForumPost
    {
        return $this->parent;
    }

    public function setParent(?ForumPost $parent): static
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return Collection<int, ForumPost>
     */
    public function getReplies(): Collection
    {
        return $this->replies;
    }

    public function addReply(ForumPost $reply): static
    {
        if (!$this->replies->contains($reply)) {
            $this->replies->add($reply);
            $reply->setParent($this);
        }
        return $this;
    }

    public function removeReply(ForumPost $reply): static
    {
        if ($this->replies->removeElement($reply)) {
            if ($reply->getParent() === $this) {
                $reply->setParent(null);
            }
        }
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
}