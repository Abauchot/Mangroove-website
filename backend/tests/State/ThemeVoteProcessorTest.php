<?php

namespace App\Tests\State;

use App\Entity\ThemeVote;
use App\Entity\ThemeProposal;
use App\Entity\User;
use App\Entity\Jam;
use App\State\ThemeVoteProcessor;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class ThemeVoteProcessorTest extends TestCase
{
    private ThemeVoteProcessor $processor;
    private EntityManagerInterface $entityManager;
    private Security $security;
    private User $user;
    private ThemeProposal $themeProposal;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->security = $this->createMock(Security::class);
        
        $this->processor = new ThemeVoteProcessor(
            $this->entityManager,
            $this->security
        );

        $this->user = new User();
        $this->user->setEmail('user@example.com');
        $this->user->setUsername('user');
        $this->user->setPassword('password');

        $jam = new Jam();
        $jam->setTitle('Test Jam');
        $jam->setStartsAt(new \DateTime('+1 day'));
        $jam->setEndsAt(new \DateTime('+7 days'));

        $this->themeProposal = new ThemeProposal();
        $this->themeProposal->setText('Test Theme');
        $this->themeProposal->setJam($jam);
        $this->themeProposal->setAuthor($this->user);
    }

    public function testProcessThemeVote(): void
    {
        $themeVote = new ThemeVote();
        $themeVote->setThemeProposal($this->themeProposal);

        $operation = $this->createMock(Operation::class);
        
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($themeVote);

        $this->entityManager
            ->expects($this->exactly(2))
            ->method('flush');

        $result = $this->processor->process($themeVote, $operation);

        $this->assertSame($themeVote, $result);
        $this->assertSame($this->user, $themeVote->getVoter());
    }

    public function testProcessWithNonThemeVoteData(): void
    {
        $nonThemeVoteData = new \stdClass();
        $operation = $this->createMock(Operation::class);

        $this->security
            ->expects($this->never())
            ->method('getUser');

        $this->entityManager
            ->expects($this->never())
            ->method('persist');

        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $result = $this->processor->process($nonThemeVoteData, $operation);

        $this->assertSame($nonThemeVoteData, $result);
    }

    public function testProcessUpdatesThemeProposalScore(): void
    {
        $themeVote = new ThemeVote();
        $themeVote->setThemeProposal($this->themeProposal);

        $operation = $this->createMock(Operation::class);
        
        $this->security
            ->method('getUser')
            ->willReturn($this->user);


        $this->assertEquals(0, $this->themeProposal->getScore());


        $this->themeProposal->addVote($themeVote);

        $this->processor->process($themeVote, $operation);
        

        $this->assertEquals(1, $this->themeProposal->getScore());
    }

    public function testProcessWithNullThemeProposal(): void
    {
        $themeVote = new ThemeVote();

        $operation = $this->createMock(Operation::class);
        
        $this->security
            ->method('getUser')
            ->willReturn($this->user);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($themeVote);

        $this->entityManager
            ->expects($this->once()) 
            ->method('flush');

        $result = $this->processor->process($themeVote, $operation);

        $this->assertSame($themeVote, $result);
        $this->assertSame($this->user, $themeVote->getVoter());
    }
}