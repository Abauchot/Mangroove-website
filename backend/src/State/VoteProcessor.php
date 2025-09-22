<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use App\Entity\Vote;
use Symfony\Bundle\SecurityBundle\Security;

final class VoteProcessor implements ProcessorInterface
{
    public function __construct(
        private PersistProcessor $persistProcessor,
        private Security $security
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Vote && $operation instanceof \ApiPlatform\Metadata\Post) {
            // assign the current user as the voter
            $currentUser = $this->security->getUser();
            if ($currentUser) {
                $data->setVoter($currentUser);
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}