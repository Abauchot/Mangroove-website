<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\VoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ApiResource(
    operations: [
        new Get(
            security : "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
        new GetCollection(
            security : "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
        new Post(
            security : "is_granted('IS_AUTHENTICATED_FULLY')",
            securityPostDenormalize: "is_granted('VOTE_CREATE', object)",
            processor: \App\State\VoteProcessor::class
        ),
        new Put(
            security: "is_granted('VOTE_EDIT', object)"
        ),
        new Delete(
            security: "is_granted('VOTE_DELETE', object)"
        )
    ],
    normalizationContext: ['groups' => ['vote:read']],
    denormalizationContext: ['groups' => ['vote:write']]
)]

#[ORM\Entity(repositoryClass: VoteRepository::class)]
#[ORM\Table(name: '`vote`')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(
    fields: ['gameEntry', 'voter'],
    message: 'You have already voted for this submission.'
)]

class Vote
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['vote:read'])]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: GameEntry::class, inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vote:read', 'vote:write'])]
    #[Assert\NotNull(message: 'Game entry must be provided.')]
    private ?GameEntry $gameEntry = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vote:read'])]
    private ?User $voter = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['vote:read', 'vote:write'])]
    #[Assert\NotNull(message: 'Score must be provided.')]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: 'Score must be between {{ min }} and {{ max }}.'
    )]
    private ?int $score = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['vote:read'])]
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

    public function getGameEntry(): ?GameEntry
    {
        return $this->gameEntry;
    }

    public function setGameEntry(?GameEntry $gameEntry): static
    {
        $this->gameEntry = $gameEntry;

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

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

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
