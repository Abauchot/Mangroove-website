<?php

namespace App\DataFixtures;

use App\Entity\Vote;
use App\Entity\User;
use App\Entity\GameEntry;
use App\Entity\Jam;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VoteFixtures extends Fixture implements OrderedFixtureInterface
{
    public function getOrder(): int
    {
        return 5; 
    }

    public function load(ObjectManager $manager): void
    {
        $userRepository = $manager->getRepository(User::class);
        $gameEntryRepository = $manager->getRepository(GameEntry::class);
        $jamRepository = $manager->getRepository(Jam::class);
        
        $users = $userRepository->findAll();
        $closedJam = $jamRepository->findOneBy(['status' => Jam::STATUS_CLOSED]);
        
        if (!$closedJam || empty($users)) {
            return; 
        }
        
        $gameEntries = $gameEntryRepository->findBy(['jam' => $closedJam]);
        
        
        foreach ($gameEntries as $gameEntry) {
            foreach ($users as $user) {
            
                if ($gameEntry->getAuthor() === $user) {
                    continue;
                }

                if (random_int(1, 10) <= 7) {
                    $vote = new Vote();
                    $vote->setGameEntry($gameEntry);
                    $vote->setVoter($user);
                    $vote->setScore(random_int(3, 5)); 
                    
                    $manager->persist($vote);
                }
            }
        }

        $manager->flush();
    }
}