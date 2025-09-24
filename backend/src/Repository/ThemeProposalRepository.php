<?php

namespace App\Repository;

use App\Entity\ThemeProposal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ThemeProposal>
 */
class ThemeProposalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThemeProposal::class);
    }

    /**
     * @return ThemeProposal[] Returns an array of ThemeProposal objects ordered by score
     */
    public function findByJamOrderedByScore(\App\Entity\Jam $jam): array
    {
        return $this->createQueryBuilder('tp')
            ->andWhere('tp.jam = :jam')
            ->setParameter('jam', $jam)
            ->orderBy('tp.score', 'DESC')
            ->addOrderBy('tp.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ThemeProposal[] Returns an array of ThemeProposal objects by jam
     */
    public function findByJam(\App\Entity\Jam $jam): array
    {
        return $this->createQueryBuilder('tp')
            ->andWhere('tp.jam = :jam')
            ->setParameter('jam', $jam)
            ->orderBy('tp.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ThemeProposal[] Returns an array of ThemeProposal objects by jam and phase
     */
    public function findByJamAndPhase(\App\Entity\Jam $jam, string $phase): array
    {
        return $this->createQueryBuilder('tp')
            ->andWhere('tp.jam = :jam')
            ->andWhere('tp.phase = :phase')
            ->setParameter('jam', $jam)
            ->setParameter('phase', $phase)
            ->orderBy('tp.score', 'DESC')
            ->addOrderBy('tp.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findTopThemeForJam(\App\Entity\Jam $jam): ?ThemeProposal
    {
        return $this->createQueryBuilder('tp')
            ->andWhere('tp.jam = :jam')
            ->setParameter('jam', $jam)
            ->orderBy('tp.score', 'DESC')
            ->addOrderBy('tp.createdAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
