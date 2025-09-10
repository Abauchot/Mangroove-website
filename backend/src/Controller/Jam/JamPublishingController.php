<?php

namespace App\Controller\Jam;

use App\Entity\Jam;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api/jams/{id}/publish', name: 'app_jam_publishing', methods: ['POST'])]
#[OA\Post(
    summary: 'Publish a new Jam',
    description: 'switch the jam to published state',
    responses: [
        new OA\Response(response: 200, description: 'Jam published successfully'),
        new OA\Response(response: 400, description: 'Invalid request'),
        new OA\Response(response: 401, description: 'Unauthorized'),
        new OA\Response(response: 404, description: 'Jam not found'),
        new OA\Response(response: 500, description: 'Server error'),
    ]
)]



final class JamPublishingController extends AbstractController
{
    public function __invoke(
        string $id,
        EntityManagerInterface $em,
    ): JsonResponse {
        $jam = $em->getRepository(Jam::class)->find($id);

        if (!$jam) {
            return new JsonResponse(['error' => 'Jam not found'], 404);
        }

        $jam->setStatus(Jam::STATUS_PUBLISHED);
        $em->flush();

        return new JsonResponse(['message' => 'Jam published successfully'], 200);
    }
}
