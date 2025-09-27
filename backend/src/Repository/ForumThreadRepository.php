<?php

namespace App\Repository;

use App\Entity\ForumThread;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ForumThread>
 */
class ForumThreadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumThread::class);
    }

    /**
     * Find threads by jam
     */
    public function findByJam($jam): array
    {
        return $this->createQueryBuilder('ft')
            ->andWhere('ft.jam = :jam')
            ->setParameter('jam', $jam)
            ->orderBy('ft.pinned', 'DESC')
            ->addOrderBy('ft.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find public threads
     */
    public function findPublicThreads(): array
    {
        return $this->createQueryBuilder('ft')
            ->andWhere('ft.isPublic = :isPublic')
            ->setParameter('isPublic', true)
            ->orderBy('ft.pinned', 'DESC')
            ->addOrderBy('ft.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find announcements
     */
    public function findAnnouncements(): array
    {
        return $this->createQueryBuilder('ft')
            ->andWhere('ft.isAnnouncement = :isAnnouncement')
            ->setParameter('isAnnouncement', true)
            ->orderBy('ft.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find pinned threads
     */
    public function findPinnedThreads(): array
    {
        return $this->createQueryBuilder('ft')
            ->andWhere('ft.pinned = :pinned')
            ->setParameter('pinned', true)
            ->orderBy('ft.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}