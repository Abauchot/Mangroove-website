<?php

namespace App\Security;

use App\Entity\ThemeVote;
use App\Entity\User;
use App\Entity\Jam;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ThemeVoteVoter extends Voter
{
    public const CREATE = 'THEME_VOTE_CREATE';
    public const DELETE = 'THEME_VOTE_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::DELETE])
            && $subject instanceof ThemeVote;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var ThemeVote $themeVote */
        $themeVote = $subject;
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::CREATE => $this->canVote($themeVote, $user),
            self::DELETE => $this->canDelete($themeVote, $user),
            default => false,
        };
    }

    private function canVote(ThemeVote $themeVote, User $user): bool
    {
        $themeProposal = $themeVote->getThemeProposal();
        if (!$themeProposal) {
            return false;
        }

        $jam = $themeProposal->getJam();
        
        // check if the jam is in a state that allows theme voting
        $allowedStatuses = [Jam::STATUS_DRAFT, Jam::STATUS_PUBLISHED];
        if (!in_array($jam->getStatus(), $allowedStatuses)) {
            return false;
        }

        // Check the theme voting period
        $now = new \DateTimeImmutable();
        if ($jam->getThemeVotingEndAt() && $now > $jam->getThemeVotingEndAt()) {
            return false;
        }

        // A user cannot vote for their own theme proposal
        return true;
    }

    private function canDelete(ThemeVote $themeVote, User $user): bool
    {
        
        if ($themeVote->getVoter() !== $user) {
            return $user->isAdmin() || $user->isModerator();
        }

        $themeProposal = $themeVote->getThemeProposal();
        $jam = $themeProposal->getJam();
        
        $now = new \DateTimeImmutable();
        if ($jam->getThemeVotingEndAt() && $now > $jam->getThemeVotingEndAt()) {
            return false;
        }

        return true;
    }
}