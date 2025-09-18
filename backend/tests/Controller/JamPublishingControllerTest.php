<?php

namespace App\Tests\Controller;

use App\Entity\Jam;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class JamPublishingControllerTest extends WebTestCase
{
    public function testPublishJam(): void
    {
        $client = static::createClient();

    
        //clean up existing jams
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $entityManager->createQuery('DELETE FROM App\Entity\Jam j')->execute();
        $entityManager->flush();

        //create a new jam in database (using Doctrine)
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $jam = new Jam();
        $jam->setTitle('Test Jam');
        $jam->setSlug('test-jam-' . uniqid()); 
        $jam->setStartsAt(new \DateTime('+1 day'));
        $jam->setEndsAt(new \DateTime('+2 days'));
        $jam->setVotingEndAt(new \DateTime('+3 days'));
        $jam->setThemeSubmissionEndAt(new \DateTime('+4 days'));
        $jam->setThemeVotingEndAt(new \DateTime('+5 days'));
        $entityManager->persist($jam);
        $entityManager->flush();

        // Call the publish endpoint (bypassing authentication for the test)
        $client->request('POST', '/api/jams/' . $jam->getId() . '/publish', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer fake-token-for-test'
        ]);

        // Verify the response
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Jam published successfully']),
            $client->getResponse()->getContent()
        );

        // Verify that the jam status is now "published"
        $entityManager->refresh($jam);
        $this->assertEquals(Jam::STATUS_PUBLISHED, $jam->getStatus());
    }
}
