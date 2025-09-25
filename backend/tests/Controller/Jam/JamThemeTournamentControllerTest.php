<?php

namespace App\Tests\Controller\Jam;

use App\Controller\Jam\JamThemeTournamentController;
use App\Entity\Jam;
use App\Entity\ThemeProposal;
use App\Entity\User;
use App\Repository\JamRepository;
use App\Repository\ThemeProposalRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class JamThemeTournamentControllerTest extends TestCase
{
    private JamThemeTournamentController $controller;
    private EntityManagerInterface $entityManager;
    private JamRepository $jamRepository;
    private ThemeProposalRepository $themeProposalRepository;
    private AuthorizationCheckerInterface $authorizationChecker;
    private Jam $jam;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->jamRepository = $this->createMock(JamRepository::class);
        $this->themeProposalRepository = $this->createMock(ThemeProposalRepository::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);

        $this->controller = new JamThemeTournamentController(
            $this->entityManager,
            $this->jamRepository,
            $this->themeProposalRepository
        );

        $reflection = new \ReflectionClass($this->controller);
        if ($reflection->hasProperty('authorizationChecker')) {
            $property = $reflection->getProperty('authorizationChecker');
            $property->setAccessible(true);
            $property->setValue($this->controller, $this->authorizationChecker);
        }

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

    public function testProcessEliminationWithInsufficientQualifiedThemes(): void
    {
        $jamRepository = $this->createMock(\App\Repository\JamRepository::class);
        $jamRepository
            ->method('find')
            ->willReturn($this->jam);

        $this->entityManager
            ->method('getRepository')
            ->with(Jam::class)
            ->willReturn($jamRepository);

        // Mock authorization
        $this->authorizationChecker
            ->method('isGranted')
            ->with('JAM_EDIT', $this->jam)
            ->willReturn(true);

        $proposals = [
            $this->createThemeProposal('Theme 1', 5),
            $this->createThemeProposal('Theme 2', 3),
        ];

        $this->themeProposalRepository
            ->method('findByJam')
            ->willReturn($proposals);

        $this->themeProposalRepository
            ->method('findByJamOrderedByScore')
            ->willReturn($proposals);

        $response = ($this->controller)($this->jam->getId());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Not enough qualified themes for tournament', $data['error']);
        $this->assertEquals(4, $data['required']);
        $this->assertEquals(2, $data['qualified']);
    }

    public function testProcessEliminationSuccess(): void
    {
        $jamRepository = $this->createMock(\App\Repository\JamRepository::class);
        $jamRepository
            ->method('find')
            ->willReturn($this->jam);

        $this->entityManager
            ->method('getRepository')
            ->with(Jam::class)
            ->willReturn($jamRepository);

        // Mock authorization
        $this->authorizationChecker
            ->method('isGranted')
            ->with('JAM_EDIT', $this->jam)
            ->willReturn(true);

        $proposals = [
            $this->createThemeProposal('Theme 1', 5),
            $this->createThemeProposal('Theme 2', 4),
            $this->createThemeProposal('Theme 3', 4),
            $this->createThemeProposal('Theme 4', 3),
            $this->createThemeProposal('Theme 5', 2),
        ];

        $this->themeProposalRepository
            ->method('findByJam')
            ->willReturn($proposals);

        $this->themeProposalRepository
            ->method('findByJamOrderedByScore')
            ->willReturn($proposals);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $response = ($this->controller)($this->jam->getId());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Elimination phase completed', $data['message']);
        $this->assertEquals(4, $data['qualified_themes']);
        $this->assertEquals(ThemeProposal::PHASE_QUARTER, $data['next_phase']);
    }

    public function testProcessQuarterToSemi(): void
    {
        $jamRepository = $this->createMock(\App\Repository\JamRepository::class);
        $jamRepository
            ->method('find')
            ->willReturn($this->jam);

        $this->entityManager
            ->method('getRepository')
            ->with(Jam::class)
            ->willReturn($jamRepository);

        // Mock authorization
        $this->authorizationChecker
            ->method('isGranted')
            ->with('JAM_EDIT', $this->jam)
            ->willReturn(true);

        $proposals = [
            $this->createThemeProposal('Theme 1', 8, ThemeProposal::PHASE_QUARTER),
            $this->createThemeProposal('Theme 2', 7, ThemeProposal::PHASE_QUARTER),
            $this->createThemeProposal('Theme 3', 6, ThemeProposal::PHASE_QUARTER),
            $this->createThemeProposal('Theme 4', 5, ThemeProposal::PHASE_QUARTER),
        ];

        $this->themeProposalRepository
            ->method('findByJam')
            ->willReturn($proposals);

        $this->themeProposalRepository
            ->method('findByJamAndPhase')
            ->with($this->jam, ThemeProposal::PHASE_QUARTER)
            ->willReturn($proposals);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $response = ($this->controller)($this->jam->getId());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Semi-finals phase completed', $data['message']);
        $this->assertEquals(2, $data['qualified_themes']);
        $this->assertEquals(ThemeProposal::PHASE_SEMI, $data['next_phase']);
    }

    public function testProcessFinalToWinner(): void
    {
        $jamRepository = $this->createMock(\App\Repository\JamRepository::class);
        $jamRepository
            ->method('find')
            ->willReturn($this->jam);

        $this->entityManager
            ->method('getRepository')
            ->with(Jam::class)
            ->willReturn($jamRepository);

    
        $this->authorizationChecker
            ->method('isGranted')
            ->with('JAM_EDIT', $this->jam)
            ->willReturn(true);

        $winnerProposal = $this->createThemeProposal('Winning Theme', 10, ThemeProposal::PHASE_FINAL);
        $proposals = [$winnerProposal];

        $this->themeProposalRepository
            ->method('findByJam')
            ->willReturn($proposals);

        $this->themeProposalRepository
            ->method('findByJamAndPhase')
            ->with($this->jam, ThemeProposal::PHASE_FINAL)
            ->willReturn($proposals);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $response = ($this->controller)($this->jam->getId());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Tournament completed!', $data['message']);
        $this->assertEquals('Winning Theme', $data['winner']);
        $this->assertEquals(10, $data['score']);
        
        $this->assertEquals('Winning Theme', $this->jam->getTheme());
    }

    public function testTournamentAlreadyCompleted(): void
    {
        $jamRepository = $this->createMock(\App\Repository\JamRepository::class);
        $jamRepository
            ->method('find')
            ->willReturn($this->jam);

        $this->entityManager
            ->method('getRepository')
            ->with(Jam::class)
            ->willReturn($jamRepository);

        $this->authorizationChecker
            ->method('isGranted')
            ->with('JAM_EDIT', $this->jam)
            ->willReturn(true);

        $proposals = [
            $this->createThemeProposal('Winner', 10, ThemeProposal::PHASE_WINNER),
        ];

        $this->themeProposalRepository
            ->method('findByJam')
            ->willReturn($proposals);

        $response = ($this->controller)($this->jam->getId());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Tournament already completed', $data['error']);
    }

    private function createThemeProposal(string $text, int $score, string $phase = ThemeProposal::PHASE_SUBMISSION): ThemeProposal
    {
        $user = new User();
        $user->setEmail("author@example.com");
        $user->setUsername("author");
        $user->setPassword("password");

        $proposal = new ThemeProposal();
        $proposal->setText($text);
        $proposal->setJam($this->jam);
        $proposal->setAuthor($user);
        $proposal->setScore($score);
        $proposal->setPhase($phase);

        return $proposal;
    }
}