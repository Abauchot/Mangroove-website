<?php

namespace App\Security;

use App\Entity\Vote;
use App\Entity\User;
use App\Entity\Jam;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class VoteVoter extends Voter
{
    public const CREATE = 'VOTE_CREATE';
    public const EDIT = 'VOTE_EDIT';
    public const DELETE = 'VOTE_DELETE';
    public const VIEW = 'VOTE_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::EDIT, self::DELETE, self::VIEW])
            && $subject instanceof Vote;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Vote $vote */
        $vote = $subject;

        return match ($attribute) {
            self::CREATE => $this->canCreate($vote, $user),
            self::EDIT => $this->canEdit($vote, $user),
            self::DELETE => $this->canDelete($vote, $user),
            self::VIEW => $this->canView($vote, $user),
            default => false,
        };
    }

    private function canCreate(Vote $vote, User $user): bool
    {
        $gameEntry = $vote->getGameEntry();
        
        if (!$gameEntry) {
            return false;
        }

        $jam = $gameEntry->getJam();
        
        // Only authenticated users can create votes
        if (!$user) {
            return false;
        }

        // Users cannot vote for their own submission
        if ($gameEntry->getAuthor() === $user) {
            return false;
        }

        // Users can only vote during the voting period (jam closed)
        if ($jam->getStatus() !== Jam::STATUS_CLOSED) {
            return false;
        }

        return true;
    }

    private function canEdit(Vote $vote, User $user): bool
    {
        // Only the author of the vote can edit it
        if ($vote->getVoter() !== $user) {
            return false;
        }

        $jam = $vote->getGameEntry()->getJam();

        // Users can only edit their vote during the voting period
        if ($jam->getStatus() !== Jam::STATUS_CLOSED) {
            return false;
        }

        return true;
    }

    private function canDelete(Vote $vote, User $user): bool
    {
        // Only the author of the vote can delete it, or an admin
        if ($vote->getVoter() === $user) {
            return true;
        }

        // Admins can delete any vote
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canView(Vote $vote, User $user): bool
    {
        // Everyone can view votes during and after the voting period
        $jam = $vote->getGameEntry()->getJam();
        
        return in_array($jam->getStatus(), [Jam::STATUS_CLOSED, Jam::STATUS_ARCHIVED]);
    }
}