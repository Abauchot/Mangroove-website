<?php

namespace App\DataFixtures;

use App\Entity\Jam;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class JamFixtures extends Fixture implements OrderedFixtureInterface
{
    public function getOrder(): int
    {
        return 2; 
    }

    public function load(ObjectManager $manager): void
    {
       
        $jamRunning = new Jam();
        $jamRunning->setTitle('Game Jam d\'Été 2024');
        $jamRunning->setSlug('summer-jam-2024');
        $jamRunning->setTheme('Nature et Survie');
        $jamRunning->setStatus(Jam::STATUS_RUNNING);
        $jamRunning->setStartsAt(new \DateTime('-2 days'));
        $jamRunning->setEndsAt(new \DateTime('+1 day'));
        $jamRunning->setVotingEndAt(new \DateTime('+3 days'));
        $jamRunning->setThemeSubmissionEndAt(new \DateTime('-5 days'));
        $jamRunning->setThemeVotingEndAt(new \DateTime('-3 days'));
        $manager->persist($jamRunning);

   
        $jamClosed = new Jam();
        $jamClosed->setTitle('Retro Game Jam 2024');
        $jamClosed->setSlug('retro-jam-2024');
        $jamClosed->setTheme('Pixel Art Rétro');
        $jamClosed->setStatus(Jam::STATUS_CLOSED);
        $jamClosed->setStartsAt(new \DateTime('-10 days'));
        $jamClosed->setEndsAt(new \DateTime('-3 days'));
        $jamClosed->setVotingEndAt(new \DateTime('+1 day'));
        $jamClosed->setThemeSubmissionEndAt(new \DateTime('-15 days'));
        $jamClosed->setThemeVotingEndAt(new \DateTime('-12 days'));
        $manager->persist($jamClosed);

     
        $jamPublished = new Jam();
        $jamPublished->setTitle('Halloween Horror Jam 2024');
        $jamPublished->setSlug('halloween-horror-2024');
        $jamPublished->setTheme('Horreur et Mystère');
        $jamPublished->setStatus(Jam::STATUS_PUBLISHED);
        $jamPublished->setStartsAt(new \DateTime('+5 days'));
        $jamPublished->setEndsAt(new \DateTime('+12 days'));
        $jamPublished->setVotingEndAt(new \DateTime('+15 days'));
        $jamPublished->setThemeSubmissionEndAt(new \DateTime('+3 days'));
        $jamPublished->setThemeVotingEndAt(new \DateTime('+4 days'));
        $manager->persist($jamPublished);

   
        $jamTest = new Jam();
        $jamTest->setTitle('Test Jam for Comments');
        $jamTest->setSlug('test-jam-comments');
        $jamTest->setTheme('Test Theme');
        $jamTest->setStatus(Jam::STATUS_RUNNING);
        $jamTest->setStartsAt(new \DateTime('-1 day'));
        $jamTest->setEndsAt(new \DateTime('+1 day'));
        $jamTest->setVotingEndAt(new \DateTime('+2 days'));
        $jamTest->setThemeSubmissionEndAt(new \DateTime('-2 days'));
        $jamTest->setThemeVotingEndAt(new \DateTime('-1 hour'));
        $manager->persist($jamTest);

        $manager->flush();
    }
}
