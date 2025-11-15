<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ThemeVote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ThemeVoteProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof ThemeVote) {
            return $data;
        }

        // assigned the current user as voter
        $data->setVoter($this->security->getUser());

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        // update the score of the associated theme proposal
        $themeProposal = $data->getThemeProposal();
        if ($themeProposal) {
            $themeProposal->updateScore();
            $this->entityManager->flush();
        }

        return $data;
    }
}