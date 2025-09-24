<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Repository\ThemeVoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource(
    operations: [
        new Post(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            securityPostDenormalize: "is_granted('THEME_VOTE_CREATE', object)",
            processor: \App\State\ThemeVoteProcessor::class
        ),
        new Delete(
            security: "is_granted('THEME_VOTE_DELETE', object)"
        )
    ],
    normalizationContext: ['groups' => ['theme_vote:read']],
    denormalizationContext: ['groups' => ['theme_vote:write']]
)]

#[ORM\Entity(repositoryClass: ThemeVoteRepository::class)]
#[ORM\Table(name: '`theme_vote`')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(
    fields: ['themeProposal', 'voter'],
    message: 'You have already voted for this theme proposal.'
)]
class ThemeVote
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['theme_vote:read'])]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: ThemeProposal::class, inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['theme_vote:read', 'theme_vote:write'])]
    #[Assert\NotNull(message: 'Theme proposal must be provided.')]
    private ?ThemeProposal $themeProposal = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['theme_vote:read'])]
    private ?User $voter = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['theme_vote:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getThemeProposal(): ?ThemeProposal
    {
        return $this->themeProposal;
    }

    public function setThemeProposal(?ThemeProposal $themeProposal): static
    {
        $this->themeProposal = $themeProposal;

        return $this;
    }

    public function getVoter(): ?User
    {
        return $this->voter;
    }

    public function setVoter(?User $voter): static
    {
        $this->voter = $voter;

        return $this;
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
}
