<?php

namespace App\Tests\Controller;

use App\Entity\Jam;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class JamStartControllerTest extends WebTestCase
{
    public function testStartJam(): void
    {
        $client = static::createClient();

        // Clean up existing jams
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $entityManager->createQuery('DELETE FROM App\Entity\Jam j')->execute();
        $entityManager->flush();

        // Create a published jam in database
        $jam = new Jam();
        $jam->setTitle('Test Jam to Start');
        $jam->setSlug('test-jam-start-' . uniqid());
        $jam->setStartsAt(new \DateTime('+1 day'));
        $jam->setEndsAt(new \DateTime('+2 days'));
        $jam->setVotingEndAt(new \DateTime('+3 days'));
        $jam->setThemeSubmissionEndAt(new \DateTime('+4 days'));
        $jam->setThemeVotingEndAt(new \DateTime('+5 days'));
        $jam->setStatus(Jam::STATUS_PUBLISHED); // Jam must be published first
        $entityManager->persist($jam);
        $entityManager->flush();

        // Call the start endpoint
        $client->request('POST', '/api/jams/' . $jam->getId() . '/start');

        // Verify the response
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Jam started successfully']),
            $client->getResponse()->getContent()
        );

        // Verify that the jam status is now "running"
        $entityManager->refresh($jam);
        $this->assertEquals(Jam::STATUS_RUNNING, $jam->getStatus());
    }

    public function testStartJamNotFound(): void
    {
        $client = static::createClient();

        // Call the start endpoint with non-existent ID
        $client->request('POST', '/api/jams/00000000-0000-0000-0000-000000000000/start');

        // Verify 404 response
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'Jam not found']),
            $client->getResponse()->getContent()
        );
    }

    public function testStartJamWrongStatus(): void
    {
        $client = static::createClient();

        // Clean up existing jams
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $entityManager->createQuery('DELETE FROM App\Entity\Jam j')->execute();
        $entityManager->flush();

        // Create a draft jam (not published)
        $jam = new Jam();
        $jam->setTitle('Draft Jam');
        $jam->setSlug('draft-jam-' . uniqid());
        $jam->setStartsAt(new \DateTime('+1 day'));
        $jam->setEndsAt(new \DateTime('+2 days'));
        $jam->setVotingEndAt(new \DateTime('+3 days'));
        $jam->setThemeSubmissionEndAt(new \DateTime('+4 days'));
        $jam->setThemeVotingEndAt(new \DateTime('+5 days'));
        // Status remains 'draft' by default
        $entityManager->persist($jam);
        $entityManager->flush();

        // Call the start endpoint
        $client->request('POST', '/api/jams/' . $jam->getId() . '/start');

        // Verify 400 response
        $this->assertResponseStatusCodeSame(400);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Jam must be in published state to be started', $response['error']);
        $this->assertEquals('draft', $response['current_status']);

        // Verify that the jam status hasn't changed
        $entityManager->refresh($jam);
        $this->assertEquals(Jam::STATUS_DRAFT, $jam->getStatus());
    }
}
