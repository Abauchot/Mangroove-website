<?php

namespace App\Security;

use App\Entity\Jam;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class JamVoter extends Voter
{
    public const VIEW = 'JAM_VIEW';
    public const EDIT = 'JAM_EDIT';
    public const DELETE = 'JAM_DELETE';
    public const PUBLISH = 'JAM_PUBLISH';
    public const START = 'JAM_START';
    public const CLOSE = 'JAM_CLOSE';
    public const ARCHIVE = 'JAM_ARCHIVE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
            self::VIEW, self::EDIT, self::DELETE,
            self::PUBLISH, self::START, self::CLOSE, self::ARCHIVE
        ]) && $subject instanceof Jam;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // user must be logged in; if not, deny access
        if (!$user instanceof User) {
            return false;
        }

        /** @var Jam $jam */
        $jam = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($jam, $user),
            self::EDIT => $this->canEdit($jam, $user),
            self::DELETE => $this->canDelete($jam, $user),
            self::PUBLISH => $this->canManageLifecycle($jam, $user),
            self::START => $this->canManageLifecycle($jam, $user),
            self::CLOSE => $this->canManageLifecycle($jam, $user),
            self::ARCHIVE => $this->canManageLifecycle($jam, $user),
            default => false,
        };
    }

    private function canView(Jam $jam, User $user): bool
    {
        // Everyone can view published jams
        if ($jam->getStatus() !== 'draft') {
            return true;
        }

        // Only moderators+ can view drafts
        return $user->isModerator();
    }

    private function canEdit(Jam $jam, User $user): bool
    {
        // Admin can edit everything
        if ($user->isAdmin()) {
            return true;
        }

        // Moderator can edit their own jams
        if ($user->isModerator() && $jam->getCreatedBy() === $user) {
            return true;
        }

        return false;
    }

    private function canDelete(Jam $jam, User $user): bool
    {
        // Same logic as editing
        return $this->canEdit($jam, $user);
    }

    private function canManageLifecycle(Jam $jam, User $user): bool
    {
        // Admin can manage the lifecycle of all jams
        if ($user->isAdmin()) {
            return true;
        }

        // Moderator can manage the lifecycle of their own jams
        if ($user->isModerator() && $jam->getCreatedBy() === $user) {
            return true;
        }

        return false;
    }
}
