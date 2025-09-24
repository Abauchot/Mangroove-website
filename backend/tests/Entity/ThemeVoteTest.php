<?php

namespace App\Tests\Entity;

use App\Entity\ThemeVote;
use App\Entity\ThemeProposal;
use App\Entity\User;
use App\Entity\Jam;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ThemeVoteTest extends TestCase
{
    private ThemeVote $themeVote;
    private ThemeProposal $themeProposal;
    private User $voter;

    protected function setUp(): void
    {
        $this->voter = new User();
        $this->voter->setEmail('voter@example.com');
        $this->voter->setUsername('voter');
        $this->voter->setPassword('password');

        $jam = new Jam();
        $jam->setTitle('Test Jam');
        $jam->setStartsAt(new \DateTime('+1 day'));
        $jam->setEndsAt(new \DateTime('+7 days'));

        $author = new User();
        $author->setEmail('author@example.com');
        $author->setUsername('author');
        $author->setPassword('password');

        $this->themeProposal = new ThemeProposal();
        $this->themeProposal->setText('Test Theme');
        $this->themeProposal->setJam($jam);
        $this->themeProposal->setAuthor($author);

        $this->themeVote = new ThemeVote();
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(Uuid::class, $this->themeVote->getId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->themeVote->getCreatedAt());
    }

    public function testSettersAndGetters(): void
    {
        $this->themeVote->setThemeProposal($this->themeProposal);
        $this->themeVote->setVoter($this->voter);

        $this->assertSame($this->themeProposal, $this->themeVote->getThemeProposal());
        $this->assertSame($this->voter, $this->themeVote->getVoter());
    }

    public function testCreatedAtIsImmutable(): void
    {
        $createdAt = $this->themeVote->getCreatedAt();
        

        $newCreatedAt = new \DateTimeImmutable('+1 hour');
        $this->themeVote->setCreatedAt($newCreatedAt);
        
        $this->assertEquals($newCreatedAt, $this->themeVote->getCreatedAt());
        $this->assertNotEquals($createdAt, $this->themeVote->getCreatedAt());
    }

    public function testRelationWithThemeProposal(): void
    {
        $this->themeVote->setThemeProposal($this->themeProposal);
        $this->themeVote->setVoter($this->voter);
        
  
        $this->themeProposal->addVote($this->themeVote);
        
   
        $this->assertTrue($this->themeProposal->getVotes()->contains($this->themeVote));
        $this->assertSame($this->themeProposal, $this->themeVote->getThemeProposal());
    }
}