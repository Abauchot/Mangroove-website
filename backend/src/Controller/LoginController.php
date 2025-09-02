<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['POST'])]
    #[OA\Post(
        summary: 'Authentification JWT',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string'),
                    new OA\Property(property: 'password', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Token JWT'),
            new OA\Response(response: 401, description: 'Bad credentials')
        ]
    )]
    public function __invoke(): Response
    {
        // Ce contrôleur ne sera jamais exécuté car géré par LexikJWT.
        return $this->json(['message' => 'Handled by LexikJWT'], 200);
    }
}
