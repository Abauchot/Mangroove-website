<?php

namespace App\Controller\Forum;

use App\Service\ForumStatsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api/forum/stats', name: 'forum_stats', methods: ['GET'])]
#[OA\Get(
    summary: 'Get forum statistics',
    description: 'Retrieve global statistics about the forum activity',
    responses: [
        new OA\Response(response: 200, description: 'Statistics retrieved successfully'),
    ]
)]
final class ForumStatsController extends AbstractController
{
    public function __invoke(ForumStatsService $forumStatsService): JsonResponse
    {
        $globalStats = $forumStatsService->getGlobalStats();
        $mostActiveThreads = $forumStatsService->getMostActiveThreads(5);
        $latestThreads = $forumStatsService->getLatestThreads(5);

        return new JsonResponse([
            'globalStats' => $globalStats,
            'mostActiveThreads' => array_map(function($result) {
                $thread = $result[0];
                return [
                    'id' => $thread->getId(),
                    'title' => $thread->getTitle(),
                    'postsCount' => $result['postsCount'],
                    'author' => $thread->getAuthor()->getUsername(),
                    'isPinned' => $thread->getPinned(),
                    'createdAt' => $thread->getCreatedAt()->format('c')
                ];
            }, $mostActiveThreads),
            'latestThreads' => array_map(function($thread) {
                return [
                    'id' => $thread->getId(),
                    'title' => $thread->getTitle(),
                    'author' => $thread->getAuthor()->getUsername(),
                    'isPinned' => $thread->getPinned(),
                    'isAnnouncement' => $thread->getIsAnnouncement(),
                    'createdAt' => $thread->getCreatedAt()->format('c')
                ];
            }, $latestThreads)
        ]);
    }
}