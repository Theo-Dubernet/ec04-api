<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api/user')]
final class UserController extends AbstractController
{
    /**
     * CREATE USER
     */
    #[Route('', methods: ['POST'])]
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
            'ADMIN' => 'ROLE_ADMIN',
            'LIVREUR' => 'ROLE_LIVREUR',
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

    /**
     * GET ALL USER
     */
    #[Route('', methods: ['GET'])]
    public function getAll(EntityManagerInterface $em): JsonResponse
    {
        $users = $em->getRepository(User::class)->findAll();

        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'roles' => $user->getRoles(),
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * GET DETAIL USER
     * Affichage des sac que pour les livreurs
     */
    #[Route('/{id}', methods: ['GET'])]
    public function getOne(int $id, EntityManagerInterface $em): JsonResponse
    {
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User non trouvé'], 404);
        }

        $roles = $user->getRoles();

        $response = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'roles' => $roles,
        ];

        if (in_array('ROLE_LIVREUR', $roles)) {

            $sacs = [];

            foreach ($user->getSacs() as $sac) {
                $sacs[] = [
                    'id' => $sac->getId(),
                    'quantite' => $sac->getQuantite(),
                    'produit' => [
                        'id' => $sac->getProduit()?->getId(),
                        'nom' => $sac->getProduit()?->getNom(),
                    ]
                ];
            }

            $response['sacs'] = $sacs;
        }

        return new JsonResponse($response);
    }

    /**
     * Patch user (uniq_constraint_username)
     */
    #[Route('/{id}', methods: ['PATCH'])]
    public function update(
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['username'])) {

            $existingUser = $em->getRepository(User::class)->findOneBy([
                'username' => $data['username']
            ]);

            if ($existingUser && $existingUser->getId() !== $user->getId()) {
                return new JsonResponse([
                    'error' => 'Username déjà utilisé'
                ], 409);
            }

            $user->setUsername($data['username']);
        }

        if (isset($data['nom'])) {
            $user->setNom($data['nom']);
        }

        if (isset($data['prenom'])) {
            $user->setPrenom($data['prenom']);
        }

        $em->flush();

        return new JsonResponse([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'roles' => $user->getRoles()
        ]);
    }

    /**
     * DELETE USER
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(
        int $id,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User non trouvé'], 404);
        }

        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, 204);
    }
}
