<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

#[Route('/register', name: 'app_register', methods: ['POST'])]
#[OA\Post(
    summary: 'Créer un utilisateur',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email'),
                new OA\Property(
                    property: 'password', 
                    type: 'string',
                    description: 'Minimun 8 characteres, one uppercase, one lowercase, one digit, one special character',
                    example: 'Password1!'
                )
            ]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: 'User created successfully'),
        new OA\Response(response: 400, description: 'Email or password missing/invalid'),
        new OA\Response(response: 409, description: 'Email already used'),
        new OA\Response(response: 429, description: 'Too many requests, please try again later'),
        new OA\Response(response: 500, description: 'Server error'),

    ]
)]
final class RegisterController extends AbstractController
{
    public function __invoke(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Missing email or password'], 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['error' => 'Invalid email format'], 400);
        }

        $email = trim($data['email']);
        if (empty($email)) {
            return new JsonResponse(['error' => 'Email cannot be empty'], 400);
        }

        $password = $data['password'];
        
        if (strlen($password) > 64) {
            return new JsonResponse(['error' => 'Password too long (max 64 characters)'], 400);
        }

        $passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/';

        if (!preg_match($passwordRegex, $password)) {
            return new JsonResponse(['error' => 
            'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one digit, and one special character.'], 
            400);
        }

        if (empty(trim($data['password']))) {
            return new JsonResponse(['error' => 'Password cannot be empty'], 400);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword(
            $passwordHasher->hashPassword($user, $data['password'])
        );

        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'User created'], 201);
    }
}
