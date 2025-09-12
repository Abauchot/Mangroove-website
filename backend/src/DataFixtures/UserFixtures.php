<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function getOrder(): int
    {
        return 1; 
    }

    public function load(ObjectManager $manager): void
    {
       
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@mangroove.com');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
        $admin->setPassword($hashedPassword);
        $manager->persist($admin);

        
        $moderator = new User();
        $moderator->setUsername('moderator');
        $moderator->setEmail('moderator@mangroove.com');
        $moderator->setRoles(['ROLE_MODERATOR', 'ROLE_USER']);
        $hashedPassword = $this->passwordHasher->hashPassword($moderator, 'mod123');
        $moderator->setPassword($hashedPassword);
        $manager->persist($moderator);

      
        $user1 = new User();
        $user1->setUsername('alice');
        $user1->setEmail('alice@example.com');
        $user1->setRoles(['ROLE_USER']);
        $hashedPassword = $this->passwordHasher->hashPassword($user1, 'alice123');
        $user1->setPassword($hashedPassword);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('bob');
        $user2->setEmail('bob@example.com');
        $user2->setRoles(['ROLE_USER']);
        $hashedPassword = $this->passwordHasher->hashPassword($user2, 'bob123');
        $user2->setPassword($hashedPassword);
        $manager->persist($user2);

        $user3 = new User();
        $user3->setUsername('charlie');
        $user3->setEmail('charlie@example.com');
        $user3->setRoles(['ROLE_USER']);
        $hashedPassword = $this->passwordHasher->hashPassword($user3, 'charlie123');
        $user3->setPassword($hashedPassword);
        $manager->persist($user3);

        $manager->flush();
    }
}
