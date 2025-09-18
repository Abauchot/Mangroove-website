<?php

namespace App\Tests\Controller;

use App\Entity\Jam;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class JamArchiveControllerTest extends WebTestCase
{
    public function testArchiveJam(): void
    {
        $client = static::createClient();

        // Clean up existing jams
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $entityManager->createQuery('DELETE FROM App\Entity\Jam j')->execute();
        $entityManager->flush();

        // Create a closed jam in database
        $jam = new Jam();
        $jam->setTitle('Test Jam to Archive');
        $jam->setSlug('test-jam-archive-' . uniqid());
        $jam->setStartsAt(new \DateTime('+1 day'));
        $jam->setEndsAt(new \DateTime('+2 days'));
        $jam->setVotingEndAt(new \DateTime('+3 days'));
        $jam->setThemeSubmissionEndAt(new \DateTime('+4 days'));
        $jam->setThemeVotingEndAt(new \DateTime('+5 days'));
        $jam->setStatus(Jam::STATUS_CLOSED); // Jam must be closed first
        $entityManager->persist($jam);
        $entityManager->flush();

        // Call the archive endpoint
        $client->request('POST', '/api/jams/' . $jam->getId() . '/archive');

        // Verify the response
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Jam archived successfully']),
            $client->getResponse()->getContent()
        );

        // Verify that the jam status is now "archived"
        $entityManager->refresh($jam);
        $this->assertEquals(Jam::STATUS_ARCHIVED, $jam->getStatus());
    }

    public function testArchiveJamNotFound(): void
    {
        $client = static::createClient();

        // Call the archive endpoint with non-existent ID
        $client->request('POST', '/api/jams/00000000-0000-0000-0000-000000000000/archive');

        // Verify 404 response
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'Jam not found']),
            $client->getResponse()->getContent()
        );
    }

    public function testArchiveJamWrongStatus(): void
    {
        $client = static::createClient();

        // Clean up existing jams
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $entityManager->createQuery('DELETE FROM App\Entity\Jam j')->execute();
        $entityManager->flush();

        // Create a running jam (not closed)
        $jam = new Jam();
        $jam->setTitle('Running Jam');
        $jam->setSlug('running-jam-' . uniqid());
        $jam->setStartsAt(new \DateTime('+1 day'));
        $jam->setEndsAt(new \DateTime('+2 days'));
        $jam->setVotingEndAt(new \DateTime('+3 days'));
        $jam->setThemeSubmissionEndAt(new \DateTime('+4 days'));
        $jam->setThemeVotingEndAt(new \DateTime('+5 days'));
        $jam->setStatus(Jam::STATUS_RUNNING); // Not closed
        $entityManager->persist($jam);
        $entityManager->flush();

        // Call the archive endpoint
        $client->request('POST', '/api/jams/' . $jam->getId() . '/archive');

        // Verify 400 response
        $this->assertResponseStatusCodeSame(400);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Jam must be in closed state to be archived', $response['error']);
        $this->assertEquals('running', $response['current_status']);

        // Verify that the jam status hasn't changed
        $entityManager->refresh($jam);
        $this->assertEquals(Jam::STATUS_RUNNING, $jam->getStatus());
    }
}
