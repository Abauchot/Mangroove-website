<?php

namespace App\Tests\Integration;

use App\Entity\User;
use App\Entity\Jam;
use App\Entity\GameEntry;
use App\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Test E2E for voting system
 *
 * This test verifies that all components work together:
 * ✅ Successful vote creation
 * ✅ Blocking vote for own submission
 * ✅ Blocking vote on non-closed jam
 * ✅ Uniqueness constraint (no double voting)
 */
class VotingTestE2E extends WebTestCase
{
    private $client;
    private $entityManager;
    private $passwordHasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $this->cleanDatabase();
    }

    protected function tearDown(): void
    {
        $this->cleanDatabase();
        parent::tearDown();
    }

    private function cleanDatabase(): void
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\Vote v')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\GameEntry g')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Jam j')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User u')->execute();
        $this->entityManager->flush();
    }

    public function testCompleteVotingSystemWorkflow(): void
    {
        echo "\n🚀 Démarrage du test E2E du système de vote\n";

        echo "1️⃣ Création des utilisateurs et données...\n";
        
        $author = $this->createUser('author@e2e.com', 'Author User');
        $voter1 = $this->createUser('voter1@e2e.com', 'Voter One');
        $voter2 = $this->createUser('voter2@e2e.com', 'Voter Two');
        
        $closedJam = $this->createJam('Closed Jam E2E', Jam::STATUS_CLOSED);
        $runningJam = $this->createJam('Running Jam E2E', Jam::STATUS_RUNNING);
        
        $gameInClosedJam = $this->createGameEntry($closedJam, $author, 'Game in Closed Jam');
        $gameInRunningJam = $this->createGameEntry($runningJam, $author, 'Game in Running Jam');

 
        echo "2️⃣ Test de vote réussi...\n";
        $this->performSuccessfulVote($voter1, $gameInClosedJam, 5);
        $this->performSuccessfulVote($voter2, $gameInClosedJam, 4);

        // ❌ ÉTAPE 3: Tests des blocages de sécurité
        echo "3️⃣ Test des restrictions de sécurité...\n";
        $this->testAuthorCannotVoteForOwnGame($author, $gameInClosedJam);
        $this->testCannotVoteOnRunningJam($voter1, $gameInRunningJam);
        $this->testCannotVoteTwice($voter1, $gameInClosedJam);


        echo "4️⃣ Vérification des statistiques...\n";
        $this->verifyVoteStatistics($gameInClosedJam, 2, 4.5);

        echo "✅ Test E2E terminé avec succès !\n";
    }

    public function testVoteApiCrud(): void
    {
        echo "\n🔗 Test des endpoints CRUD de l'API Vote\n";

 
        $author = $this->createUser('api-author@test.com', 'API Author');
        $voter = $this->createUser('api-voter@test.com', 'API Voter'); 
        $jam = $this->createJam('API Test Jam', Jam::STATUS_CLOSED);
        $gameEntry = $this->createGameEntry($jam, $author, 'API Test Game');

        $this->client->loginUser($voter);


        echo "📋 GET /api/votes (vide)...\n";
        $this->client->request('GET', '/api/votes');
        $this->assertResponseIsSuccessful();

        echo "➕ POST /api/votes (création)...\n";
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
        $voteData = json_decode($this->client->getResponse()->getContent(), true);
        $voteId = $voteData['id'];
        $this->assertEquals(4, $voteData['score']);

    
        echo "📖 GET /api/votes/{id} (lecture)...\n";
        $this->client->request('GET', '/api/votes/' . $voteId);
        $this->assertResponseIsSuccessful();
        $voteData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(4, $voteData['score']);

   
        echo "✏️ PUT /api/votes/{id} (modification)...\n";
        $this->client->request(
            'PUT',
            '/api/votes/' . $voteId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode(['score' => 5])
        );

        $this->assertResponseIsSuccessful();
        $voteData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(5, $voteData['score']);

        echo "✅ API CRUD fonctionne parfaitement !\n";
    }

    private function createUser(string $email, string $name): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        return $user;
    }

    private function createJam(string $title, string $status): Jam
    {
        $jam = new Jam();
        $jam->setTitle($title);
        $jam->setSlug(strtolower(str_replace(' ', '-', $title)) . '-' . uniqid());
        $jam->setStatus($status);
        $jam->setStartsAt(new \DateTime('-2 days'));
        $jam->setEndsAt(new \DateTime('-1 day'));
        $jam->setVotingEndAt(new \DateTime('+1 day'));
        $jam->setThemeSubmissionEndAt(new \DateTime('-5 days'));
        $jam->setThemeVotingEndAt(new \DateTime('-3 days'));
        
        $this->entityManager->persist($jam);
        $this->entityManager->flush();
        
        return $jam;
    }

    private function createGameEntry(Jam $jam, User $author, string $title): GameEntry
    {
        $gameEntry = new GameEntry();
        $gameEntry->setTitle($title);
        $gameEntry->setDescription('Game created for E2E testing');
        $gameEntry->setAuthor($author);
        $gameEntry->setJam($jam);
        $gameEntry->setIsPublic(true);
        $gameEntry->setPlayUrl('https://example.com/game');
        
        $this->entityManager->persist($gameEntry);
        $this->entityManager->flush();
        
        return $gameEntry;
    }

    private function performSuccessfulVote(User $voter, GameEntry $gameEntry, int $score): void
    {
        $this->client->loginUser($voter);
        
        $this->client->request(
            'POST',
            '/api/votes',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode([
                'gameEntry' => '/api/game_entries/' . $gameEntry->getId(),
                'score' => $score
            ])
        );
        
        $this->assertResponseStatusCodeSame(201, 
            sprintf('Vote de %s devrait réussir', $voter->getEmail())
        );
        
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($score, $response['score']);
        
        echo sprintf("   ✅ Vote de %s créé (score: %d)\n", $voter->getEmail(), $score);
    }

    private function testAuthorCannotVoteForOwnGame(User $author, GameEntry $gameEntry): void
    {
        $this->client->loginUser($author);
        
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
        
        $this->assertResponseStatusCodeSame(403, 
            'Auteur ne devrait pas pouvoir voter pour sa propre soumission'
        );
        
        echo "   ❌ Auteur bloqué pour vote sur sa propre soumission ✓\n";
    }

    private function testCannotVoteOnRunningJam(User $voter, GameEntry $gameEntry): void
    {
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
        
        $this->assertResponseStatusCodeSame(403, 
            'Vote sur jam non fermée devrait être interdit'
        );
        
        echo "   ❌ Vote sur jam running bloqué ✓\n";
    }

    private function testCannotVoteTwice(User $voter, GameEntry $gameEntry): void
    {
        $this->client->loginUser($voter);
        
        $this->client->request(
            'POST',
            '/api/votes',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode([
                'gameEntry' => '/api/game_entries/' . $gameEntry->getId(),
                'score' => 3
            ])
        );
        
        // Devrait échouer (422 = contrainte d'unicité, ou 403 = access denied)
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertTrue(
            in_array($statusCode, [403, 422]), 
            'Double vote devrait être interdit (403 ou 422)'
        );
        
        echo "   ❌ Double vote bloqué (status: {$statusCode}) ✓\n";
    }

    private function verifyVoteStatistics(GameEntry $gameEntry, int $expectedCount, float $expectedAverage): void
    {
        $votes = $this->entityManager
            ->getRepository(Vote::class)
            ->findBy(['gameEntry' => $gameEntry]);
            
        $this->assertCount($expectedCount, $votes, 
            sprintf('Devrait avoir %d votes', $expectedCount)
        );
        
        if ($expectedCount > 0) {
            $totalScore = array_sum(array_map(fn($vote) => $vote->getScore(), $votes));
            $averageScore = $totalScore / count($votes);
            
            $this->assertEquals($expectedAverage, $averageScore, 
                sprintf('Score moyen devrait être %.1f', $expectedAverage)
            );
            
            echo sprintf("   📊 %d votes, moyenne: %.1f ✓\n", $expectedCount, $averageScore);
        }
    }
}