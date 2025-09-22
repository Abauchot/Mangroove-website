<?php

namespace App\Tests\Controller;

use App\Entity\Vote;
use App\Entity\User;
use App\Entity\GameEntry;
use App\Entity\Jam;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VoteControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
      
        $this->entityManager->createQuery('DELETE FROM App\Entity\Vote v')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\GameEntry g')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Jam j')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User u')->execute();
        $this->entityManager->flush();
        
     
        self::ensureKernelShutdown();
        
        parent::tearDown();
    }

    public function testCreateVoteSuccess(): void
    {

        $author = new User();
        $author->setEmail('author@test.com');
        $author->setPassword('password');
        
        $voter = new User();
        $voter->setEmail('voter@test.com');
        $voter->setPassword('password');

        $jam = new Jam();
        $jam->setTitle('Test Jam');
        $jam->setSlug('test-jam-vote');
        $jam->setStatus(Jam::STATUS_CLOSED); 
        $jam->setStartsAt(new \DateTime('+1 day'));
        $jam->setEndsAt(new \DateTime('+2 days'));
        $jam->setVotingEndAt(new \DateTime('+3 days'));
        $jam->setThemeSubmissionEndAt(new \DateTime('+4 days'));
        $jam->setThemeVotingEndAt(new \DateTime('+5 days'));

        $gameEntry = new GameEntry();
        $gameEntry->setTitle('Test Game');
        $gameEntry->setDescription('Test description');
        $gameEntry->setAuthor($author);
        $gameEntry->setJam($jam);
        $gameEntry->setIsPublic(true);
        $gameEntry->setPlayUrl('https://example.com/game');

        $this->entityManager->persist($author);
        $this->entityManager->persist($voter);
        $this->entityManager->persist($jam);
        $this->entityManager->persist($gameEntry);
        $this->entityManager->flush();


        $this->client->loginUser($voter);

   

        $this->client->request(
            'POST',
            '/api/votes',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode([
                'gameEntry' => '/api/game_entries/' . $gameEntry->getId(),
                'score' => 4
            ])
        );

        $this->assertResponseStatusCodeSame(201);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(4, $response['score']);
    }

    public function testCannotVoteForOwnGameEntry(): void
    {
  
        $user = new User();
        $user->setEmail('user@test.com');
        $user->setPassword('password');

        $jam = new Jam();
        $jam->setTitle('Test Jam');
        $jam->setSlug('test-jam-own-vote');
        $jam->setStatus(Jam::STATUS_CLOSED);
        $jam->setStartsAt(new \DateTime('+1 day'));
        $jam->setEndsAt(new \DateTime('+2 days'));
        $jam->setVotingEndAt(new \DateTime('+3 days'));
        $jam->setThemeSubmissionEndAt(new \DateTime('+4 days'));
        $jam->setThemeVotingEndAt(new \DateTime('+5 days'));

        $gameEntry = new GameEntry();
        $gameEntry->setTitle('My Own Game');
        $gameEntry->setDescription('Test description');
        $gameEntry->setAuthor($user);
        $gameEntry->setJam($jam);
        $gameEntry->setIsPublic(true);
        $gameEntry->setPlayUrl('https://example.com/game');

        $this->entityManager->persist($user);
        $this->entityManager->persist($jam);
        $this->entityManager->persist($gameEntry);
        $this->entityManager->flush();

        $this->client->loginUser($user);

      
        $this->client->request(
            'POST',
            '/api/votes',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode([
                'gameEntry' => '/api/game_entries/' . $gameEntry->getId(),
                'score' => 5
            ])
        );

        $this->assertResponseStatusCodeSame(403);
    }
}