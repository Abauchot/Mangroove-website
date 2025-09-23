<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\ThemeProposalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiRessource(
    opreations: [
        new Get(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
        new GetCollection(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
        new Post(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            securityPostDenormalize: "is_granted('THEME_PROPOSAL_CREATE', object)",
        ),
        new Put(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
        new Delete(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
        )
    ],
    normalizationContext: ['groups' => ['themeProposal:read']],
    denormalizationContext: ['groups' => ['themeProposal:write']],
)]


#[ORM\Entity(repositoryClass: ThemeProposalRepository::class)]
#[ORM\Table(name: 'theme_proposals')]
#[UniqueEntity(
    fields: ['text','jam'], 
    message: 'This theme has already been proposed for this jam.'
    )]
class ThemeProposal
{
    #[ORM\Id]
    #[ORM\Column(type:'uuid', unique: true)]
    #[Groups(['themeProposal:read'])]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: Jam::class, inversedBy: 'themeProposals')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['themeProposal:read', 'themeProposal:write'])]
    #[Assert\NotNull(message: 'Jam must be provided')]
    private ?Jam $jam = null;

    #[ORM\ManyToOne(targetEntity: User::class,)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['themeProposal:read'])]
    private ?User $author = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Groups(['themeProposal:read', 'themeProposal:write'])]
    #[Assert\NotBlank(message: 'Text must not be blank')]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Text must be at least {{ limit }} characters long',
        maxMessage: 'Text must not exceed {{ limit }} characters'
    )]
    private ?string $text = null;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    #[Groups(['themeProposal:read'])]
    private ?int $score = 0;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['themeProposal:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(targetEntity: ThemeVote::class, mappedBy: 'themeProposal', orphanRemoval: true)]
    private Collection $votes;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->createdAt = new \DateTimeImmutable();
        $this->votes = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

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

    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(ThemeVote $vote): static
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setThemeProposal($this);
        }

        return $this;
    }

    public function removeVote(ThemeVote $vote): static
    {
        if ($this->votes->removeElement($vote)) {
            if ($vote->getThemeProposal() === $this) {
                $vote->setThemeProposal(null);
            }
        }

        return $this;
    }

    public function updateScore(): void {
        $this->score = $this->votes->count();
    }
}
