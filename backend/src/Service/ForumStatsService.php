<?php

namespace App\Service;

use App\Entity\ForumThread;
use App\Repository\ForumThreadRepository;
use Doctrine\ORM\EntityManagerInterface;

class ForumStatsService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ForumThreadRepository $threadRepository
    ) {}

    public function updateThreadStats(): void
    {
        $threads = $this->threadRepository->findAll();
        
        foreach ($threads as $thread) {
            $posts = $thread->getForumPosts();
            $postsCount = $posts->count();
            
            $thread->setPostsCount($postsCount);
            
            if (!$posts->isEmpty()) {
                $lastPost = $posts->last();
                $thread->setLastActivityAt($lastPost->getCreatedAt());
            }
        }
        
        $this->em->flush();
    }

    public function getGlobalStats(): array
    {
        $totalThreads = $this->threadRepository->count([]);
        $publicThreadsCount = $this->threadRepository->count(['isPublic' => true]);
        $pinnedThreadsCount = $this->threadRepository->count(['pinned' => true]);
        $announcementsCount = $this->threadRepository->count(['isAnnouncement' => true]);
        
        $conn = $this->em->getConnection();
        $totalPostsResult = $conn->executeQuery('SELECT COUNT(*) FROM forum_post');
        $totalPosts = $totalPostsResult->fetchOne();
        
        return [
            'totalThreads' => $totalThreads,
            'publicThreads' => $publicThreadsCount,
            'pinnedThreads' => $pinnedThreadsCount,
            'announcements' => $announcementsCount,
            'totalPosts' => (int) $totalPosts,
            'updatedAt' => new \DateTime()
        ];
    }

    public function getMostActiveThreads(int $limit = 10): array
    {
        return $this->em->createQuery('
            SELECT t, COUNT(p) as postsCount
            FROM App\Entity\ForumThread t
            LEFT JOIN t.forumPosts p
            WHERE t.isPublic = true
            GROUP BY t
            ORDER BY postsCount DESC, t.updatedAt DESC
        ')
        ->setMaxResults($limit)
        ->getResult();
    }

    public function getLatestThreads(int $limit = 10): array
    {
        return $this->threadRepository->createQueryBuilder('t')
            ->where('t.isPublic = true')
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}