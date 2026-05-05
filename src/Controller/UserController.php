<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserController extends AbstractController
{
    #[Route('/api/users', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['username'], $data['password'], $data['nom'], $data['prenom'])) {
            return new JsonResponse(['error' => 'Données invalides'], 400);
        }

        $user = new User();
        $user->setUsername($data['username']);
        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);

        $allowedRoles = [
            'USER' => 'ROLE_USER',
            'ADMIN' => 'ROLE_ADMIN'
        ];

        $roleInput = $data['role'] ?? 'USER';

        if (!isset($allowedRoles[$roleInput])) {
            return new JsonResponse(['error' => 'Rôle invalide'], 400);
        }

        $user->setRoles([$allowedRoles[$roleInput]]);

        $hashedPassword = $hasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ], 201);
    }
}
