<?php

namespace App\Repository;

use App\Entity\Vote;
use App\Entity\GameEntry;
use App\Entity\User;
use App\Entity\Jam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vote>
 */
class VoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    public function findVotesByUserAndJam(User $user, Jam $jam): array
    {
        return $this->createQueryBuilder('v')
            ->join('v.gameEntry', 'ge')
            ->where('v.voter = :user')
            ->andWhere('ge.jam = :jam')
            ->setParameter('user', $user)
            ->setParameter('jam', $jam)
            ->getQuery()
            ->getResult();
    }

    public function getAverageScoreForGameEntry(GameEntry $gameEntry): ?float
    {
        $result = $this->createQueryBuilder('v')
            ->select('AVG(v.score) as average')
            ->where('v.gameEntry = :gameEntry')
            ->setParameter('gameEntry', $gameEntry)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : null;
    }

    public function countVotesForGameEntry(GameEntry $gameEntry): int
    {
        return $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->where('v.gameEntry = :gameEntry')
            ->setParameter('gameEntry', $gameEntry)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function hasUserVotedForGameEntry(User $user, GameEntry $gameEntry): bool
    {
        $vote = $this->findOneBy([
            'voter' => $user,
            'gameEntry' => $gameEntry
        ]);

        return $vote !== null;
    }

    public function getJamRankingByAverageScore(Jam $jam): array
    {
        return $this->createQueryBuilder('v')
            ->select('ge.id, ge.title, AVG(v.score) as averageScore, COUNT(v.id) as voteCount')
            ->join('v.gameEntry', 'ge')
            ->where('ge.jam = :jam')
            ->groupBy('ge.id, ge.title')
            ->orderBy('averageScore', 'DESC')
            ->addOrderBy('voteCount', 'DESC')
            ->setParameter('jam', $jam)
            ->getQuery()
            ->getResult();
    }
}
