<?php

namespace App\DataFixtures;

use App\Entity\GameEntry;
use App\Entity\User;
use App\Entity\Jam;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GameEntryFixtures extends Fixture implements OrderedFixtureInterface
{
    public function getOrder(): int
    {
        return 3; 
    }

    public function load(ObjectManager $manager): void
    {
       
        $userRepository = $manager->getRepository(User::class);
        $jamRepository = $manager->getRepository(Jam::class);
        
        $users = $userRepository->findAll();
        $runningJam = $jamRepository->findOneBy(['slug' => 'summer-jam-2024']);
        $closedJam = $jamRepository->findOneBy(['slug' => 'retro-jam-2024']);
        $testJam = $jamRepository->findOneBy(['slug' => 'test-jam-comments']);

        if (empty($users) || !$runningJam || !$closedJam || !$testJam) {
            throw new \Exception('Users or Jams not found - load order may be incorrect');
        }

        
        $gameEntry1 = new GameEntry();
        $gameEntry1->setTitle('Forest Survival Adventure');
        $gameEntry1->setDescription('Un jeu de survie en forêt où vous devez collecter des ressources et construire un abri avant la nuit.');
        $gameEntry1->setAuthor($users[1]); 
        $gameEntry1->setJam($runningJam);
        $gameEntry1->setIsPublic(true);
        $gameEntry1->setPlayUrl('https://example.com/forest-survival');
        $gameEntry1->addMediaUrl('https://github.com/user/forest-survival');
        $gameEntry1->addMediaUrl('https://example.com/screenshots/forest.png');
        $manager->persist($gameEntry1);

   
        $gameEntry2 = new GameEntry();
        $gameEntry2->setTitle('Nature\'s Wrath');
        $gameEntry2->setDescription('Incarnez un esprit de la nature défendant la forêt contre les envahisseurs.');
        $gameEntry2->setAuthor($users[2] ?? $users[1]);
        $gameEntry2->setJam($runningJam);
        $gameEntry2->setIsPublic(true);
        $gameEntry2->setPlayUrl('https://example.com/natures-wrath');
        $gameEntry2->addMediaUrl('https://example.com/screenshots/nature.png');
        $manager->persist($gameEntry2);

     
        $gameEntry3 = new GameEntry();
        $gameEntry3->setTitle('Pixel Quest Retro');
        $gameEntry3->setDescription('Un RPG rétro en pixel art avec des mécaniques old-school.');
        $gameEntry3->setAuthor($users[3] ?? $users[0]);
        $gameEntry3->setJam($closedJam);
        $gameEntry3->setIsPublic(true);
        $gameEntry3->setPlayUrl('https://example.com/pixel-quest');
        $gameEntry3->addMediaUrl('https://github.com/user/pixel-quest');
        $gameEntry3->addMediaUrl('https://example.com/screenshots/pixel.png');
        $manager->persist($gameEntry3);

    
        $gameEntryTest = new GameEntry();
        $gameEntryTest->setTitle('Test Game for Comments');
        $gameEntryTest->setDescription('Jeu simple pour tester les commentaires via Postman.');
        $gameEntryTest->setAuthor($users[1]);
        $gameEntryTest->setJam($testJam);
        $gameEntryTest->setIsPublic(true);
        $gameEntryTest->setPlayUrl('https://example.com/test-game');
        $manager->persist($gameEntryTest);

   
        $gameEntryDraft = new GameEntry();
        $gameEntryDraft->setTitle('Work in Progress Game');
        $gameEntryDraft->setDescription('Jeu encore en développement.');
        $gameEntryDraft->setAuthor($users[2] ?? $users[0]);
        $gameEntryDraft->setJam($runningJam);
        $gameEntryDraft->setPlayUrl('https://example.com/wip-game'); 
        $gameEntryDraft->setIsPublic(false); 
        $manager->persist($gameEntryDraft);

        $manager->flush();
    }
}
