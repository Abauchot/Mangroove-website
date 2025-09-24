<?php

namespace App\Tests\Security;

use App\Entity\ThemeVote;
use App\Entity\ThemeProposal;
use App\Entity\User;
use App\Entity\Jam;
use App\Security\ThemeVoteVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ThemeVoteVoterTest extends TestCase
{
    private ThemeVoteVoter $voter;
    private TokenInterface $token;
    private User $user;
    private Jam $jam;
    private ThemeProposal $themeProposal;
    private ThemeVote $themeVote;

    protected function setUp(): void
    {
        $this->voter = new ThemeVoteVoter();
        $this->token = $this->createMock(TokenInterface::class);
        
        $this->user = new User();
        $this->user->setEmail('user@example.com');
        $this->user->setUsername('user');
        $this->user->setPassword('password');
        
        $this->jam = new Jam();
        $this->jam->setTitle('Test Jam');
        $this->jam->setStartsAt(new \DateTime('+1 day'));
        $this->jam->setEndsAt(new \DateTime('+7 days'));
        $this->jam->setStatus(Jam::STATUS_PUBLISHED);
        
        $author = new User();
        $author->setEmail('author@example.com');
        $author->setUsername('author');
        $author->setPassword('password');
        
        $this->themeProposal = new ThemeProposal();
        $this->themeProposal->setText('Test Theme');
        $this->themeProposal->setJam($this->jam);
        $this->themeProposal->setAuthor($author);
        
        $this->themeVote = new ThemeVote();
        $this->themeVote->setThemeProposal($this->themeProposal);
        $this->themeVote->setVoter($this->user);
        
        $this->token->method('getUser')->willReturn($this->user);
    }

    public function testSupportsThemeVoteCreateAttribute(): void
    {
        $result = $this->voter->vote($this->token, $this->themeVote, [ThemeVoteVoter::CREATE]);
        $this->assertNotEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testSupportsThemeVoteDeleteAttribute(): void
    {
        $result = $this->voter->vote($this->token, $this->themeVote, [ThemeVoteVoter::DELETE]);
        $this->assertNotEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testDoesNotSupportInvalidAttribute(): void
    {
        $result = $this->voter->vote($this->token, $this->themeVote, ['INVALID_ATTRIBUTE']);
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testDoesNotSupportInvalidSubject(): void
    {
        $invalidSubject = new \stdClass();
        $result = $this->voter->vote($this->token, $invalidSubject, [ThemeVoteVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testCanVoteWhenJamIsPublished(): void
    {
        $this->jam->setStatus(Jam::STATUS_PUBLISHED);
        
        $result = $this->voter->vote($this->token, $this->themeVote, [ThemeVoteVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testCanVoteWhenJamIsDraft(): void
    {
        $this->jam->setStatus(Jam::STATUS_DRAFT);
        
        $result = $this->voter->vote($this->token, $this->themeVote, [ThemeVoteVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testCannotVoteWhenJamIsRunning(): void
    {
        $this->jam->setStatus(Jam::STATUS_RUNNING);
        
        $result = $this->voter->vote($this->token, $this->themeVote, [ThemeVoteVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testCannotVoteWhenJamIsClosed(): void
    {
        $this->jam->setStatus(Jam::STATUS_CLOSED);
        
        $result = $this->voter->vote($this->token, $this->themeVote, [ThemeVoteVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testCannotVoteWhenThemeVotingPeriodIsOver(): void
    {
        $this->jam->setThemeVotingEndAt(new \DateTimeImmutable('-1 day'));
        
        $result = $this->voter->vote($this->token, $this->themeVote, [ThemeVoteVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testCanVoteWhenThemeVotingPeriodIsActive(): void
    {
        $this->jam->setThemeVotingEndAt(new \DateTimeImmutable('+1 day'));
        
        $result = $this->voter->vote($this->token, $this->themeVote, [ThemeVoteVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testCanDeleteOwnVote(): void
    {
        $result = $this->voter->vote($this->token, $this->themeVote, [ThemeVoteVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testCannotDeleteOtherUserVote(): void
    {
        $otherUser = new User();
        $otherUser->setEmail('other@example.com');
        $otherUser->setUsername('other');
        $otherUser->setPassword('password');
        
        $this->themeVote->setVoter($otherUser);
        
        $result = $this->voter->vote($this->token, $this->themeVote, [ThemeVoteVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testAdminCanDeleteAnyVote(): void
    {
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setUsername('admin');
        $admin->setPassword('password');
        $admin->setRoles([User::ROLE_ADMIN]);
        
        $otherUser = new User();
        $otherUser->setEmail('other@example.com');
        $otherUser->setUsername('other');
        $otherUser->setPassword('password');
        
        $this->themeVote->setVoter($otherUser);
        
        $adminToken = $this->createMock(TokenInterface::class);
        $adminToken->method('getUser')->willReturn($admin);
        
        $result = $this->voter->vote($adminToken, $this->themeVote, [ThemeVoteVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testModeratorCanDeleteAnyVote(): void
    {
        $moderator = new User();
        $moderator->setEmail('moderator@example.com');
        $moderator->setUsername('moderator');
        $moderator->setPassword('password');
        $moderator->setRoles([User::ROLE_MODERATOR]);
        
        $otherUser = new User();
        $otherUser->setEmail('other@example.com');
        $otherUser->setUsername('other');
        $otherUser->setPassword('password');
        
        $this->themeVote->setVoter($otherUser);
        
        $moderatorToken = $this->createMock(TokenInterface::class);
        $moderatorToken->method('getUser')->willReturn($moderator);
        
        $result = $this->voter->vote($moderatorToken, $this->themeVote, [ThemeVoteVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testCannotDeleteVoteWhenVotingPeriodIsOver(): void
    {
        $this->jam->setThemeVotingEndAt(new \DateTimeImmutable('-1 day'));
        
        $result = $this->voter->vote($this->token, $this->themeVote, [ThemeVoteVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testDeniesAccessForAnonymousUser(): void
    {
        $anonymousToken = $this->createMock(TokenInterface::class);
        $anonymousToken->method('getUser')->willReturn(null);
        
        $result = $this->voter->vote($anonymousToken, $this->themeVote, [ThemeVoteVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }
}