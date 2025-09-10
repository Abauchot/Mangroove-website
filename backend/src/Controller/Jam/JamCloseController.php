<?php

namespace App\Controller\Jam;

use App\Entity\Jam;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api/jams/{id}/close', name: 'app_jam_close', methods: ['POST'])]
#[OA\Post(
    summary: 'Close a Jam',
    description: 'Switch the jam from running to closed state (end submissions, start voting)',
    responses: [
        new OA\Response(response: 200, description: 'Jam closed successfully'),
        new OA\Response(response: 400, description: 'Invalid request or jam not in running state'),
        new OA\Response(response: 401, description: 'Unauthorized'),
        new OA\Response(response: 404, description: 'Jam not found'),
        new OA\Response(response: 500, description: 'Server error'),
    ]
)]
final class JamCloseController extends AbstractController
{
    public function __invoke(
        string $id,
        EntityManagerInterface $em,
    ): JsonResponse {
        $jam = $em->getRepository(Jam::class)->find($id);

        if (!$jam) {
            return new JsonResponse(['error' => 'Jam not found'], 404);
        }

        if ($jam->getStatus() !== Jam::STATUS_RUNNING) {
            return new JsonResponse([
                'error' => 'Jam must be in running state to be closed',
                'current_status' => $jam->getStatus()
            ], 400);
        }

        $jam->setStatus(Jam::STATUS_CLOSED);
        $em->flush();

        return new JsonResponse(['message' => 'Jam closed successfully'], 200);
    }
}
