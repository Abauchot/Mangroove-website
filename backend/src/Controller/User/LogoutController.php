<?php

namespace App\Controller\User;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/logout', name: 'app_logout', methods: ['POST'])]
#[OA\Post(
    summary: 'Déconnexion JWT (côté client uniquement)',
    description: 'Cette route est facultative. Le logout consiste simplement à supprimer le token JWT côté client.',
    responses: [
        new OA\Response(response: 204, description: 'Token oublié côté client')
    ]
)]
final class LogoutController extends AbstractController
{
    public function __invoke(): Response
    {
        return new JsonResponse(['message' => 'Déconnexion côté client (aucune action serveur)'], 204);
    }
}
