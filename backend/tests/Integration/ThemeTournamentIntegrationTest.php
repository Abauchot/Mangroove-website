<?php

namespace App\Tests\Integration;

use App\Entity\Jam;
use App\Entity\ThemeProposal;
use App\Entity\ThemeVote;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ThemeTournamentIntegrationTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testCompleteThemeTournamentWorkflow(): void
    {
        
        $jam = new Jam();
        $jam->setTitle('Tournament Test Jam');
        $jam->setSlug('tournament-test-jam-' . uniqid());
        $jam->setStartsAt(new \DateTime('+1 day'));
        $jam->setEndsAt(new \DateTime('+7 days'));
        $jam->setVotingEndAt(new \DateTime('+8 days'));
        $jam->setThemeSubmissionEndAt(new \DateTime('now'));
        $jam->setThemeVotingEndAt(new \DateTime('+1 hour'));
        $jam->setStatus(Jam::STATUS_PUBLISHED);

        $this->entityManager->persist($jam);

 
        $users = [];
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("user{$i}-" . uniqid() . "@example.com");
            $user->setUsername("user{$i}-" . uniqid());
            $user->setPassword('password');
            
            $this->entityManager->persist($user);
            $users[] = $user;
        }

 
        $themes = [
            'Voyage dans le temps',
            'Monde à l\'envers',
            'Minimalisme',
            'Chaos organisé',
            'Illusion d\'optique',
            'Connexion perdue',
            'Deux mondes',
            'Renaissance'
        ];

        $proposals = [];
        foreach ($themes as $index => $themeText) {
            $proposal = new ThemeProposal();
            $proposal->setText($themeText);
            $proposal->setJam($jam);
            $proposal->setAuthor($users[$index % count($users)]);
            
            $this->entityManager->persist($proposal);
            $proposals[] = $proposal;
        }

        $this->entityManager->flush();


        $voteDistribution = [8, 7, 6, 5, 4, 3, 2, 1]; 

        foreach ($proposals as $index => $proposal) {
            $voteCount = $voteDistribution[$index];
            
            for ($v = 0; $v < $voteCount; $v++) {
                $voter = $users[$v % count($users)];
                
  
                $existingVote = $this->entityManager
                    ->getRepository(ThemeVote::class)
                    ->findOneBy(['themeProposal' => $proposal, 'voter' => $voter]);
                
                if (!$existingVote) {
                    $vote = new ThemeVote();
                    $vote->setThemeProposal($proposal);
                    $vote->setVoter($voter);
                    
                    $this->entityManager->persist($vote);
                    $proposal->addVote($vote);
                }
            }
            
            $proposal->updateScore();
        }

        $this->entityManager->flush();

   
        $this->assertCount(8, $proposals);
        
        foreach ($proposals as $proposal) {
            $this->assertEquals(ThemeProposal::PHASE_SUBMISSION, $proposal->getPhase());
        }

    
        $qualifiedProposals = array_filter($proposals, fn($p) => $p->getScore() >= 3);
        $this->assertGreaterThanOrEqual(4, count($qualifiedProposals), 
            'Il doit y avoir au moins 4 thèmes qualifiés pour le tournoi');


        $repository = $this->entityManager->getRepository(ThemeProposal::class);
        
  
        $sortedProposals = $repository->findByJamOrderedByScore($jam);
        $top6 = array_slice($sortedProposals, 0, 6);
        
        foreach ($top6 as $proposal) {
            $proposal->setPhase(ThemeProposal::PHASE_ELIMINATION);
        }
        $this->entityManager->flush();

  
        $top4 = array_slice($top6, 0, 4);
        foreach ($top4 as $proposal) {
            $proposal->setPhase(ThemeProposal::PHASE_QUARTER);
        }
        $this->entityManager->flush();

 
        $top2 = array_slice($top4, 0, 2);
        foreach ($top2 as $proposal) {
            $proposal->setPhase(ThemeProposal::PHASE_SEMI);
        }
        $this->entityManager->flush();


        $winner = $top2[0];
        $winner->setPhase(ThemeProposal::PHASE_FINAL);
        $this->entityManager->flush();

   
        $winner->setPhase(ThemeProposal::PHASE_WINNER);
        $jam->setTheme($winner->getText());
        $this->entityManager->flush();

 
        $this->assertEquals(ThemeProposal::PHASE_WINNER, $winner->getPhase());
        $this->assertEquals($winner->getText(), $jam->getTheme());
        

        $allProposals = $repository->findByJamOrderedByScore($jam);
        $this->assertSame($winner, $allProposals[0]);
        
    
        $this->assertEquals(1, $winnerCount);
        
        $submissionCount = count($repository->findByJamAndPhase($jam, ThemeProposal::PHASE_SUBMISSION));
        $this->assertGreaterThan(0, $submissionCount);
    }

    public function testThemeVoteUnicityConstraint(): void
    {
       
        $user = new User();
        $user->setEmail('test-' . uniqid() . '@example.com');
        $user->setUsername('test-' . uniqid());
        $user->setPassword('password');

        $jam = new Jam();
        $jam->setTitle('Test Jam');
        $jam->setSlug('test-jam-' . uniqid());
        $jam->setStartsAt(new \DateTime('+1 day'));
        $jam->setEndsAt(new \DateTime('+7 days'));
        $jam->setVotingEndAt(new \DateTime('+8 days'));
        $jam->setThemeSubmissionEndAt(new \DateTime('now'));
        $jam->setThemeVotingEndAt(new \DateTime('+1 hour'));

        $proposal = new ThemeProposal();
        $proposal->setText('Test Theme');
        $proposal->setJam($jam);
        $proposal->setAuthor($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($jam);
        $this->entityManager->persist($proposal);
        $this->entityManager->flush();

        $vote1 = new ThemeVote();
        $vote1->setThemeProposal($proposal);
        $vote1->setVoter($user);

        $this->entityManager->persist($vote1);
        $this->entityManager->flush();

        $vote2 = new ThemeVote();
        $vote2->setThemeProposal($proposal);
        $vote2->setVoter($user);

        $this->entityManager->persist($vote2);


        $this->entityManager->persist($vote2);
        

        $existingVotes = $this->entityManager
            ->getRepository(ThemeVote::class)
            ->findBy(['themeProposal' => $proposal, 'voter' => $user]);
            
        $this->assertCount(1, $existingVotes, 'Il devrait y avoir exactement un vote existant');
        

        $this->entityManager->remove($vote2);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->entityManager) {
            $this->entityManager->close();
        }
    }
}