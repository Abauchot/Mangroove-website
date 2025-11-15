<?php

namespace App\Tests\Controller\Jam;

use App\Controller\Jam\JamThemeTournamentController;
use App\Entity\Jam;
use App\Entity\ThemeProposal;
use App\Repository\JamRepository;
use App\Repository\ThemeProposalRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class JamThemeTournamentControllerSimpleTest extends TestCase
{
    private JamThemeTournamentController $controller;
    private EntityManagerInterface $entityManager;
    private JamRepository $jamRepository;
    private ThemeProposalRepository $themeProposalRepository;
    private Jam $jam;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->jamRepository = $this->createMock(JamRepository::class);
        $this->themeProposalRepository = $this->createMock(ThemeProposalRepository::class);

        $this->controller = new JamThemeTournamentController(
            $this->entityManager,
            $this->jamRepository,
            $this->themeProposalRepository
        );

        $this->jam = new Jam();
        $this->jam->setTitle('Test Jam');
        $this->jam->setStartsAt(new \DateTime('+1 day'));
        $this->jam->setEndsAt(new \DateTime('+7 days'));
    }

    public function testJamNotFound(): void
    {
        $this->jamRepository
            ->method('find')
            ->with('non-existent-id')
            ->willReturn(null);

        $response = ($this->controller)('non-existent-id');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Jam not found', $data['error']);
    }

    public function testControllerConstruction(): void
    {
        $this->assertInstanceOf(JamThemeTournamentController::class, $this->controller);
    }
}