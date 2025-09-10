<?php

namespace App\Tests\Controller;

use App\Entity\Jam;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class JamCloseControllerTest extends WebTestCase
{
    public function testCloseJam(): void
    {
        $client = static::createClient();

        // Clean up existing jams
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $entityManager->createQuery('DELETE FROM App\Entity\Jam j')->execute();
        $entityManager->flush();

        // Create a running jam in database
        $jam = new Jam();
        $jam->setTitle('Test Jam to Close');
        $jam->setSlug('test-jam-close-' . uniqid());
        $jam->setStartsAt(new \DateTime('+1 day'));
        $jam->setEndsAt(new \DateTime('+2 days'));
        $jam->setVotingEndAt(new \DateTime('+3 days'));
        $jam->setThemeSubmissionEndAt(new \DateTime('+4 days'));
        $jam->setThemeVotingEndAt(new \DateTime('+5 days'));
        $jam->setStatus(Jam::STATUS_RUNNING); // Jam must be running first
        $entityManager->persist($jam);
        $entityManager->flush();

        // Call the close endpoint
        $client->request('POST', '/api/jams/' . $jam->getId() . '/close');

        // Verify the response
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Jam closed successfully']),
            $client->getResponse()->getContent()
        );

        // Verify that the jam status is now "closed"
        $entityManager->refresh($jam);
        $this->assertEquals(Jam::STATUS_CLOSED, $jam->getStatus());
    }

    public function testCloseJamNotFound(): void
    {
        $client = static::createClient();

        // Call the close endpoint with non-existent ID
        $client->request('POST', '/api/jams/00000000-0000-0000-0000-000000000000/close');

        // Verify 404 response
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'Jam not found']),
            $client->getResponse()->getContent()
        );
    }

    public function testCloseJamWrongStatus(): void
    {
        $client = static::createClient();

        // Clean up existing jams
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $entityManager->createQuery('DELETE FROM App\Entity\Jam j')->execute();
        $entityManager->flush();

        // Create a published jam (not running)
        $jam = new Jam();
        $jam->setTitle('Published Jam');
        $jam->setSlug('published-jam-' . uniqid());
        $jam->setStartsAt(new \DateTime('+1 day'));
        $jam->setEndsAt(new \DateTime('+2 days'));
        $jam->setVotingEndAt(new \DateTime('+3 days'));
        $jam->setThemeSubmissionEndAt(new \DateTime('+4 days'));
        $jam->setThemeVotingEndAt(new \DateTime('+5 days'));
        $jam->setStatus(Jam::STATUS_PUBLISHED); // Not running
        $entityManager->persist($jam);
        $entityManager->flush();

        // Call the close endpoint
        $client->request('POST', '/api/jams/' . $jam->getId() . '/close');

        // Verify 400 response
        $this->assertResponseStatusCodeSame(400);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Jam must be in running state to be closed', $response['error']);
        $this->assertEquals('published', $response['current_status']);

        // Verify that the jam status hasn't changed
        $entityManager->refresh($jam);
        $this->assertEquals(Jam::STATUS_PUBLISHED, $jam->getStatus());
    }
}
