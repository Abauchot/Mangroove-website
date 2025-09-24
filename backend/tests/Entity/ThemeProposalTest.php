<?php

namespace App\Tests\Entity;

use App\Entity\ThemeProposal;
use App\Entity\ThemeVote;
use App\Entity\User;
use App\Entity\Jam;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ThemeProposalTest extends TestCase
{
    private ThemeProposal $themeProposal;
    private User $user;
    private Jam $jam;

    protected function setUp(): void
    {
        $this->user = new User();
        $this->user->setEmail('test@example.com');
        $this->user->setUsername('testuser');
        $this->user->setPassword('password');

        $this->jam = new Jam();
        $this->jam->setTitle('Test Jam');
        $this->jam->setStartsAt(new \DateTime('+1 day'));
        $this->jam->setEndsAt(new \DateTime('+7 days'));

        $this->themeProposal = new ThemeProposal();
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(Uuid::class, $this->themeProposal->getId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->themeProposal->getCreatedAt());
        $this->assertEquals(ThemeProposal::PHASE_SUBMISSION, $this->themeProposal->getPhase());
        $this->assertEquals(0, $this->themeProposal->getScore());
        $this->assertCount(0, $this->themeProposal->getVotes());
    }

    public function testSettersAndGetters(): void
    {
        $text = 'Voyage dans le temps';
        
        $this->themeProposal->setText($text);
        $this->themeProposal->setJam($this->jam);
        $this->themeProposal->setAuthor($this->user);
        $this->themeProposal->setScore(5);
        $this->themeProposal->setPhase(ThemeProposal::PHASE_QUARTER);

        $this->assertEquals($text, $this->themeProposal->getText());
        $this->assertSame($this->jam, $this->themeProposal->getJam());
        $this->assertSame($this->user, $this->themeProposal->getAuthor());
        $this->assertEquals(5, $this->themeProposal->getScore());
        $this->assertEquals(ThemeProposal::PHASE_QUARTER, $this->themeProposal->getPhase());
    }

    public function testPhaseConstants(): void
    {
        $this->assertEquals('submission', ThemeProposal::PHASE_SUBMISSION);
        $this->assertEquals('elimination', ThemeProposal::PHASE_ELIMINATION);
        $this->assertEquals('quarter', ThemeProposal::PHASE_QUARTER);
        $this->assertEquals('semi', ThemeProposal::PHASE_SEMI);
        $this->assertEquals('final', ThemeProposal::PHASE_FINAL);
        $this->assertEquals('winner', ThemeProposal::PHASE_WINNER);
    }

    public function testIsInPhase(): void
    {
        $this->assertTrue($this->themeProposal->isInPhase(ThemeProposal::PHASE_SUBMISSION));
        $this->assertFalse($this->themeProposal->isInPhase(ThemeProposal::PHASE_QUARTER));

        $this->themeProposal->setPhase(ThemeProposal::PHASE_SEMI);
        $this->assertTrue($this->themeProposal->isInPhase(ThemeProposal::PHASE_SEMI));
        $this->assertFalse($this->themeProposal->isInPhase(ThemeProposal::PHASE_SUBMISSION));
    }

    public function testCanAdvanceToNextPhase(): void
    {
        
        $this->themeProposal->setScore(2);
        $this->assertFalse($this->themeProposal->canAdvanceToNextPhase());

        $this->themeProposal->setScore(3);
        $this->assertTrue($this->themeProposal->canAdvanceToNextPhase());

        $this->themeProposal->setPhase(ThemeProposal::PHASE_QUARTER);
        $this->themeProposal->setScore(1);
        $this->assertTrue($this->themeProposal->canAdvanceToNextPhase());


        $this->themeProposal->setPhase(ThemeProposal::PHASE_WINNER);
        $this->assertFalse($this->themeProposal->canAdvanceToNextPhase());
    }

    public function testVoteManagement(): void
    {
        $voter1 = new User();
        $voter1->setEmail('voter1@example.com');
        $voter1->setUsername('voter1');
        $voter1->setPassword('password');

        $voter2 = new User();
        $voter2->setEmail('voter2@example.com');
        $voter2->setUsername('voter2');
        $voter2->setPassword('password');

        $vote1 = new ThemeVote();
        $vote1->setThemeProposal($this->themeProposal);
        $vote1->setVoter($voter1);

        $vote2 = new ThemeVote();
        $vote2->setThemeProposal($this->themeProposal);
        $vote2->setVoter($voter2);

        $this->themeProposal->addVote($vote1);
        $this->themeProposal->addVote($vote2);

        $this->assertCount(2, $this->themeProposal->getVotes());
        $this->assertTrue($this->themeProposal->getVotes()->contains($vote1));
        $this->assertTrue($this->themeProposal->getVotes()->contains($vote2));

        // Test remove vote
        $this->themeProposal->removeVote($vote1);
        $this->assertCount(1, $this->themeProposal->getVotes());
        $this->assertFalse($this->themeProposal->getVotes()->contains($vote1));
    }

    public function testUpdateScore(): void
    {
        $this->assertEquals(0, $this->themeProposal->getScore());

        $vote1 = new ThemeVote();
        $vote1->setVoter($this->user);
        $this->themeProposal->addVote($vote1);

        $vote2 = new ThemeVote();
        $voter2 = new User();
        $voter2->setEmail('voter2@example.com');
        $voter2->setUsername('voter2');
        $voter2->setPassword('password');
        $vote2->setVoter($voter2);
        $this->themeProposal->addVote($vote2);
        $this->themeProposal->updateScore();
        $this->assertEquals(2, $this->themeProposal->getScore());
    }
}