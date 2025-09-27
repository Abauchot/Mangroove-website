<?php

namespace App\Controller\Forum;

use App\Entity\ForumThread;
use App\Entity\ForumPost;
use App\Entity\User;
use App\Repository\ForumThreadRepository;
use App\Repository\ForumPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('/api/forum/threads/{id}/posts', name: 'forum_thread_posts', methods: ['GET'])]
#[OA\Get(
    summary: 'Get posts from a forum thread',
    description: 'Retrieve all posts from a specific forum thread with their replies',
    responses: [
        new OA\Response(response: 200, description: 'Posts retrieved successfully'),
        new OA\Response(response: 404, description: 'Thread not found'),
    ]
)]
final class ForumThreadPostsController extends AbstractController
{
    public function __invoke(
        string $id,
        ForumThreadRepository $threadRepository,
        ForumPostRepository $postRepository,
        SerializerInterface $serializer,
        #[CurrentUser] ?User $user
    ): JsonResponse {
        $thread = $threadRepository->find($id);
        if (!$thread) {
            return new JsonResponse(['error' => 'Thread not found'], 404);
        }
       
        if (!$thread->getIsPublic() && !$user) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $rootPosts = $postRepository->findRootPostsByThread($thread);
        
        $postsData = [];
        foreach ($rootPosts as $post) {
            $postData = [
                'id' => $post->getId(),
                'content' => $post->getContent(),
                'createdAt' => $post->getCreatedAt()->format('c'),
                'author' => [
                    'id' => $post->getAuthor()->getId(),
                    'username' => $post->getAuthor()->getUsername()
                ],
                'replies' => []
            ];

      
            $replies = $postRepository->findReplies($post);
            foreach ($replies as $reply) {
                $postData['replies'][] = [
                    'id' => $reply->getId(),
                    'content' => $reply->getContent(),
                    'createdAt' => $reply->getCreatedAt()->format('c'),
                    'author' => [
                        'id' => $reply->getAuthor()->getId(),
                        'username' => $reply->getAuthor()->getUsername()
                    ]
                ];
            }

            $postsData[] = $postData;
        }

        return new JsonResponse([
            'thread' => [
                'id' => $thread->getId(),
                'title' => $thread->getTitle(),
                'isLocked' => $thread->getLocked(),
                'isPinned' => $thread->getPinned(),
                'isAnnouncement' => $thread->getIsAnnouncement()
            ],
            'posts' => $postsData,
            'totalPosts' => count($thread->getForumPosts())
        ]);
    }
}