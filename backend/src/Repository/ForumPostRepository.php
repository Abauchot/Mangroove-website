<?php

namespace App\Repository;

use App\Entity\ForumPost;
use App\Entity\ForumThread;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ForumPost>
 */
class ForumPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumPost::class);
    }

    /**
     * Find posts by thread
     */
    public function findByThread(ForumThread $thread): array
    {
        return $this->createQueryBuilder('fp')
            ->andWhere('fp.thread = :thread')
            ->setParameter('thread', $thread)
            ->orderBy('fp.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find root posts (posts without parent) by thread
     */
    public function findRootPostsByThread(ForumThread $thread): array
    {
        return $this->createQueryBuilder('fp')
            ->andWhere('fp.thread = :thread')
            ->andWhere('fp.parent IS NULL')
            ->setParameter('thread', $thread)
            ->orderBy('fp.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find replies to a post
     */
    public function findReplies(ForumPost $post): array
    {
        return $this->createQueryBuilder('fp')
            ->andWhere('fp.parent = :parent')
            ->setParameter('parent', $post)
            ->orderBy('fp.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find posts by author
     */
    public function findByAuthor($author): array
    {
        return $this->createQueryBuilder('fp')
            ->andWhere('fp.author = :author')
            ->setParameter('author', $author)
            ->orderBy('fp.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}