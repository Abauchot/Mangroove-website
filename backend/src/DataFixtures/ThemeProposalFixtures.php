<?php

namespace App\DataFixtures;

use App\Entity\ThemeProposal;
use App\Entity\ThemeVote;
use App\Entity\User;
use App\Entity\Jam;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ThemeProposalFixtures extends Fixture implements OrderedFixtureInterface
{
    public function getOrder(): int
    {
        return 5; 
    }

    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();
        $jams = $manager->getRepository(Jam::class)->findBy(['status' => [Jam::STATUS_DRAFT, Jam::STATUS_PUBLISHED]]);

        if (empty($users) || empty($jams)) {
            return;
        }

        $jam = $jams[0]; 

        $themes = [
            'Voyage dans le temps',
            'Monde à l\'envers', 
            'Minimalisme',
            'Chaos organisé',
            'Illusion d\'optique',
            'Connexion perdue',
            'Deux mondes',
            'Renaissance',
            'Symbiose',
            'Gravité inversée'
        ];

        $proposals = [];

      
        foreach ($themes as $index => $themeText) {
            $proposal = new ThemeProposal();
            $proposal->setText($themeText);
            $proposal->setJam($jam);
            $proposal->setAuthor($users[$index % count($users)]);
            
            $manager->persist($proposal);
            $proposals[] = $proposal;
        }

        $manager->flush(); 

    
        $voteDistribution = [
            8, 7, 6, 5, 4, 3, 2, 1, 1, 0 
        ];

        foreach ($proposals as $index => $proposal) {
            $voteCount = $voteDistribution[$index] ?? 0;
            
           
            $voters = array_slice($users, 0, $voteCount);
            
            foreach ($voters as $voter) {
                
                $existingVote = $manager->getRepository(ThemeVote::class)
                    ->findOneBy(['themeProposal' => $proposal, 'voter' => $voter]);
                
                if (!$existingVote) {
                    $vote = new ThemeVote();
                    $vote->setThemeProposal($proposal);
                    $vote->setVoter($voter);
                    
                    $manager->persist($vote);
                    $proposal->addVote($vote);
                }
            }
            
            $proposal->updateScore();
        }

        $manager->flush();
    }
}