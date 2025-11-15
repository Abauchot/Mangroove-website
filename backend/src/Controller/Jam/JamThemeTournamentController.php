<?php

namespace App\Controller\Jam;

use App\Entity\Jam;
use App\Entity\ThemeProposal;
use App\Repository\JamRepository;
use App\Repository\ThemeProposalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api/jams/{id}/themes/tournament', name: 'app_jam_theme_tournament', methods: ['POST'])]
#[OA\Post(
    summary: 'Advance theme tournament to next phase',
    description: 'Process tournament elimination: submission → elimination → quarter → semi → final → winner',
    responses: [
        new OA\Response(response: 200, description: 'Tournament phase advanced successfully'),
        new OA\Response(response: 400, description: 'Invalid request or tournament requirements not met'),
        new OA\Response(response: 404, description: 'Jam not found'),
    ]
)]
final class JamThemeTournamentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private JamRepository $jamRepository,
        private ThemeProposalRepository $themeProposalRepository
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        $jam = $this->jamRepository->find($id);

        if (!$jam) {
            return new JsonResponse(['error' => 'Jam not found'], 404);
        }

        // check permissions (admin/moderator)
        $this->denyAccessUnlessGranted('JAM_EDIT', $jam);

        $currentPhase = $this->getCurrentPhase($jam);
        
        return match ($currentPhase) {
            ThemeProposal::PHASE_SUBMISSION => $this->processElimination($jam),
            ThemeProposal::PHASE_ELIMINATION => $this->processQuarter($jam),
            ThemeProposal::PHASE_QUARTER => $this->processSemi($jam),
            ThemeProposal::PHASE_SEMI => $this->processFinal($jam),
            ThemeProposal::PHASE_FINAL => $this->processWinner($jam),
            default => new JsonResponse(['error' => 'Tournament already completed'], 400)
        };
    }

    private function getCurrentPhase(Jam $jam): string
    {
        $proposals = $this->themeProposalRepository->findByJam($jam);
        
        if (empty($proposals)) {
            return ThemeProposal::PHASE_SUBMISSION;
        }

        // Determine the highest phase among proposals
        $phases = array_unique(array_map(fn($p) => $p->getPhase(), $proposals));
        
        if (in_array(ThemeProposal::PHASE_WINNER, $phases)) {
            return ThemeProposal::PHASE_WINNER;
        }
        
        $phaseOrder = [
            ThemeProposal::PHASE_SUBMISSION,
            ThemeProposal::PHASE_ELIMINATION,
            ThemeProposal::PHASE_QUARTER,
            ThemeProposal::PHASE_SEMI,
            ThemeProposal::PHASE_FINAL
        ];
        
        foreach (array_reverse($phaseOrder) as $phase) {
            if (in_array($phase, $phases)) {
                return $phase;
            }
        }
        
        return ThemeProposal::PHASE_SUBMISSION;
    }

    private function processElimination(Jam $jam): JsonResponse
    {
        $minVotes = 3; //minimum votes to qualify
        
        $proposals = $this->themeProposalRepository->findByJamOrderedByScore($jam);
        $qualified = array_filter($proposals, fn($p) => $p->getScore() >= $minVotes);
        
        if (count($qualified) < 4) {
            return new JsonResponse([
                'error' => 'Not enough qualified themes for tournament',
                'required' => 4,
                'qualified' => count($qualified),
                'min_votes' => $minVotes
            ], 400);
        }

        // Mark qualified themes for elimination phase
        foreach ($qualified as $proposal) {
            $proposal->setPhase(ThemeProposal::PHASE_ELIMINATION);
        }

        $this->em->flush();

        return new JsonResponse([
            'message' => 'Elimination phase completed',
            'qualified_themes' => count($qualified),
            'next_phase' => ThemeProposal::PHASE_QUARTER
        ]);
    }

    private function processQuarter(Jam $jam): JsonResponse
    {
        return $this->processPhaseElimination(
            $jam, 
            ThemeProposal::PHASE_ELIMINATION,
            ThemeProposal::PHASE_QUARTER,
            4, // Top 4 for quarter-finals
            'Quarter-finals phase completed'
        );
    }

    private function processSemi(Jam $jam): JsonResponse
    {
        return $this->processPhaseElimination(
            $jam, 
            ThemeProposal::PHASE_QUARTER,
            ThemeProposal::PHASE_SEMI,
            2, // Top 2 for semi-finals
            'Semi-finals phase completed'
        );
    }

    private function processFinal(Jam $jam): JsonResponse
    {
        return $this->processPhaseElimination(
            $jam, 
            ThemeProposal::PHASE_SEMI,
            ThemeProposal::PHASE_FINAL,
            1, // Top 1 for final
            'Final phase completed'
        );
    }

    private function processWinner(Jam $jam): JsonResponse
    {
        $finalists = $this->themeProposalRepository->findByJamAndPhase($jam, ThemeProposal::PHASE_FINAL);
        
        if (empty($finalists)) {
            return new JsonResponse(['error' => 'No finalists found'], 400);
        }

        $winner = $finalists[0]; // The most voted
        $winner->setPhase(ThemeProposal::PHASE_WINNER);

        // Update the jam's theme
        $jam->setTheme($winner->getText());
        
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Tournament completed!',
            'winner' => $winner->getText(),
            'score' => $winner->getScore()
        ]);
    }

    private function processPhaseElimination(
        Jam $jam, 
        string $currentPhase, 
        string $nextPhase, 
        int $qualifiedCount, 
        string $message
    ): JsonResponse {
        $proposals = $this->themeProposalRepository->findByJamAndPhase($jam, $currentPhase);
        
        if (count($proposals) < $qualifiedCount) {
            return new JsonResponse([
                'error' => "Not enough themes in {$currentPhase} phase",
                'required' => $qualifiedCount,
                'available' => count($proposals)
            ], 400);
        }

        // Take the top N themes
        $qualified = array_slice($proposals, 0, $qualifiedCount);
        
        foreach ($qualified as $proposal) {
            $proposal->setPhase($nextPhase);
        }

        $this->em->flush();

        return new JsonResponse([
            'message' => $message,
            'qualified_themes' => count($qualified),
            'next_phase' => $nextPhase
        ]);
    }
}