<?php

namespace App\Controller\Jam;

use App\Entity\Jam;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api/jams/{id}/archive', name: 'app_jam_archive', methods: ['POST'])]
#[OA\Post(
    summary: 'Archive a Jam',
    description: 'Switch the jam from closed to archived state (final state, results published)',
    responses: [
        new OA\Response(response: 200, description: 'Jam archived successfully'),
        new OA\Response(response: 400, description: 'Invalid request or jam not in closed state'),
        new OA\Response(response: 401, description: 'Unauthorized'),
        new OA\Response(response: 404, description: 'Jam not found'),
        new OA\Response(response: 500, description: 'Server error'),
    ]
)]
final class JamArchiveController extends AbstractController
{
    public function __invoke(
        string $id,
        EntityManagerInterface $em,
    ): JsonResponse {
        $jam = $em->getRepository(Jam::class)->find($id);

        if (!$jam) {
            return new JsonResponse(['error' => 'Jam not found'], 404);
        }

        // Vérifier que la jam est bien dans l'état "closed"
        if ($jam->getStatus() !== Jam::STATUS_CLOSED) {
            return new JsonResponse([
                'error' => 'Jam must be in closed state to be archived',
                'current_status' => $jam->getStatus()
            ], 400);
        }

        $jam->setStatus(Jam::STATUS_ARCHIVED);
        $em->flush();

        return new JsonResponse(['message' => 'Jam archived successfully'], 200);
    }
}
