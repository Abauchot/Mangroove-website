<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\JamRepository;
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

    public function __construct()
    {
        $this->id = Uuid::v4();
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
}
