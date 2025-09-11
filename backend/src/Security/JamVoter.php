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

        // L'utilisateur doit être connecté
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
        // Tout le monde peut voir les jams publiées
        if ($jam->getStatus() !== 'draft') {
            return true;
        }

        // Seuls les modérateurs+ peuvent voir les brouillons
        return $user->isModerator();
    }

    private function canEdit(Jam $jam, User $user): bool
    {
        // Admin peut tout modifier
        if ($user->isAdmin()) {
            return true;
        }

        // Modérateur peut modifier ses propres jams
        if ($user->isModerator() && $jam->getCreatedBy() === $user) {
            return true;
        }

        return false;
    }

    private function canDelete(Jam $jam, User $user): bool
    {
        // Même logique que l'édition
        return $this->canEdit($jam, $user);
    }

    private function canManageLifecycle(Jam $jam, User $user): bool
    {
        // Admin peut gérer le cycle de vie de toutes les jams
        if ($user->isAdmin()) {
            return true;
        }

        // Modérateur peut gérer le cycle de vie de ses propres jams
        if ($user->isModerator() && $jam->getCreatedBy() === $user) {
            return true;
        }

        return false;
    }
}
