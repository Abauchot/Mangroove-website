<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RoleManagementService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Promouvoir un utilisateur (seulement par un admin)
     */
    public function promoteUser(User $user, string $newRole, User $promoter): void
    {
        if (!$promoter->isAdmin()) {
            throw new AccessDeniedException('Seuls les administrateurs peuvent promouvoir des utilisateurs.');
        }

        $validPromotions = [
            User::ROLE_USER => [User::ROLE_MODERATOR],
            User::ROLE_MODERATOR => [User::ROLE_ADMIN],
        ];

        $currentHighestRole = $this->getHighestRole($user);
        
        if (!isset($validPromotions[$currentHighestRole]) || 
            !in_array($newRole, $validPromotions[$currentHighestRole])) {
            throw new \InvalidArgumentException('Promotion invalide.');
        }

        $user->addRole($newRole);
        $this->entityManager->flush();
    }

    /**
     * Rétrograder un utilisateur (seulement par un admin)
     */
    public function demoteUser(User $user, string $roleToRemove, User $demoter): void
    {
        if (!$demoter->isAdmin()) {
            throw new AccessDeniedException('Seuls les administrateurs peuvent rétrograder des utilisateurs.');
        }

        if ($roleToRemove === User::ROLE_USER) {
            throw new \InvalidArgumentException('Impossible de retirer le rôle ROLE_USER.');
        }

        $user->removeRole($roleToRemove);
        $this->entityManager->flush();
    }

    /**
     * Obtenir le rôle le plus élevé d'un utilisateur
     */
    public function getHighestRole(User $user): string
    {
        $roles = $user->getRoles();
        
        if (in_array(User::ROLE_ADMIN, $roles)) {
            return User::ROLE_ADMIN;
        }
        
        if (in_array(User::ROLE_MODERATOR, $roles)) {
            return User::ROLE_MODERATOR;
        }
        
        return User::ROLE_USER;
    }

    /**
     * Vérifier si un utilisateur peut gérer un autre utilisateur
     */
    public function canManageUser(User $manager, User $target): bool
    {
        // Un admin peut gérer tout le monde sauf les autres admins
        if ($manager->isAdmin()) {
            return !$target->isAdmin() || $manager === $target;
        }

        // Un modérateur peut seulement gérer les utilisateurs normaux
        if ($manager->isModerator()) {
            return !$target->isModerator() && !$target->isAdmin();
        }

        // Un utilisateur normal ne peut gérer personne d'autre que lui-même
        return $manager === $target;
    }
}
