<?php

namespace App\Controller\Forum;

use App\Entity\ForumThread;
use App\Entity\User;
use App\Repository\ForumThreadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use OpenApi\Attributes as OA;

#[Route('/api/forum/threads/{id}/pin', name: 'forum_thread_pin', methods: ['POST'])]
#[OA\Post(
    summary: 'Pin/Unpin a forum thread',
    description: 'Toggle the pinned status of a forum thread (moderators and admins only)',
    responses: [
        new OA\Response(response: 200, description: 'Thread pinned/unpinned successfully'),
        new OA\Response(response: 403, description: 'Access denied'),
        new OA\Response(response: 404, description: 'Thread not found'),
    ]
)]
final class ForumThreadPinController extends AbstractController
{
    public function __invoke(
        string $id,
        EntityManagerInterface $em,
        ForumThreadRepository $threadRepository,
        #[CurrentUser] User $user
    ): JsonResponse {
       
        if (!$this->isGranted('ROLE_MODERATOR') && !$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $thread = $threadRepository->find($id);
        if (!$thread) {
            return new JsonResponse(['error' => 'Thread not found'], 404);
        }

  
        $thread->setPinned(!$thread->getPinned());
        $em->flush();

        return new JsonResponse([
            'message' => $thread->getPinned() ? 'Thread pinned successfully' : 'Thread unpinned successfully',
            'pinned' => $thread->getPinned()
        ]);
    }
}