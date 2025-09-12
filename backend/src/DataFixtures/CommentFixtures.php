<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\GameEntry;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements OrderedFixtureInterface
{
    public function getOrder(): int
    {
        return 4; 
    }

    public function load(ObjectManager $manager): void
    {
      
        $userRepository = $manager->getRepository(User::class);
        $gameEntryRepository = $manager->getRepository(GameEntry::class);
        
        $users = $userRepository->findAll();
        $gameEntries = $gameEntryRepository->findAll();

        if (empty($users) || empty($gameEntries)) {
            throw new \Exception('Users or GameEntries not found - load order may be incorrect');
        }

        
        $forestGame = $gameEntryRepository->findOneBy(['title' => 'Forest Survival Adventure']);
        $naturesWrathGame = $gameEntryRepository->findOneBy(['title' => 'Nature\'s Wrath']);
        $testGame = $gameEntryRepository->findOneBy(['title' => 'Test Game for Comments']);

      
        if ($forestGame) {
            $comment1 = new Comment();
            $comment1->setContent('Excellent jeu ! L\'ambiance de survie est vraiment bien rendue.');
            $comment1->setAuthor($users[2] ?? $users[0]);
            $comment1->setGameEntry($forestGame);
            $comment1->setIsModerated(false); 
            $manager->persist($comment1);

            $comment2 = new Comment();
            $comment2->setContent('Les graphismes sont magnifiques, mais je trouve le gameplay un peu répétitif.');
            $comment2->setAuthor($users[3] ?? $users[1]);
            $comment2->setGameEntry($forestGame);
            $comment2->setIsModerated(false); 
            $manager->persist($comment2);

            $comment3 = new Comment();
            $comment3->setContent('Bravo pour ce jeu ! J\'adore le concept de construction d\'abri.');
            $comment3->setAuthor($users[0]); 
            $comment3->setGameEntry($forestGame);
            $comment3->setIsModerated(false); 
            $manager->persist($comment3);
        }

        // Commentaires sur Nature's Wrath
        if ($naturesWrathGame) {
            $comment4 = new Comment();
            $comment4->setContent('L\'idée d\'incarner un esprit de la nature est géniale !');
            $comment4->setAuthor($users[1]);
            $comment4->setGameEntry($naturesWrathGame);
            $comment4->setIsModerated(false);
            $manager->persist($comment4);

            $comment5 = new Comment();
            $comment5->setContent('Les contrôles sont fluides et l\'histoire captivante.');
            $comment5->setAuthor($users[3] ?? $users[0]);
            $comment5->setGameEntry($naturesWrathGame);
            $comment5->setIsModerated(false);
            $manager->persist($comment5);
        }

    
        if ($testGame) {
            $commentTest = new Comment();
            $commentTest->setContent('Commentaire de test pour les API calls Postman.');
            $commentTest->setAuthor($users[1]);
            $commentTest->setGameEntry($testGame);
            $commentTest->setIsModerated(false); 
            $manager->persist($commentTest);
        }


        if ($forestGame) {
            $commentModerated = new Comment();
            $commentModerated->setContent('Ce commentaire a été modéré...');
            $commentModerated->setAuthor($users[2] ?? $users[0]);
            $commentModerated->setGameEntry($forestGame);
            $commentModerated->setIsModerated(true); 
            $manager->persist($commentModerated);
        }

        $manager->flush();
    }
}
