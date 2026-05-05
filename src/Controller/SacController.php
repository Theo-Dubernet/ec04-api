<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Sac;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/sac')]
final class SacController extends AbstractController
{
    /**
     * AJOUT PRODUIT AU SAC
     */
    #[Route('', methods: ['POST'])]
    public function add(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        if (!isset($data['user_id'], $data['produit_id'], $data['quantite'])) {
            return new JsonResponse(['error' => 'Données invalides'], 400);
        }

        $user = $em->getRepository(User::class)->find($data['user_id']);
        $produit = $em->getRepository(Produit::class)->find($data['produit_id']);
        $quantite = (int) $data['quantite'];

        if (!$user || !$produit) {
            return new JsonResponse(['error' => 'User ou produit introuvable'], 404);
        }

        if ($quantite <= 0) {
            return new JsonResponse(['error' => 'Quantité invalide'], 400);
        }

        $sac = $em->getRepository(Sac::class)->findOneBy([
            'user' => $user,
            'produit' => $produit
        ]);

        if ($sac) {
            $sac->setQuantite($sac->getQuantite() + $quantite);
        } else {
            $sac = new Sac();
            $sac->setUser($user);
            $sac->setProduit($produit);
            $sac->setQuantite($quantite);

            $em->persist($sac);
        }

        $em->flush();

        return new JsonResponse([
            'message' => 'Produit ajouté au sac',
            'produit' => $produit->getNom(),
            'quantite' => $sac->getQuantite()
        ]);
    }

    /**
     * RETIRER PRODUIT DU SAC
     */
    #[Route('', methods: ['DELETE'])]
    public function remove(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        if (!isset($data['user_id'], $data['produit_id'], $data['quantite'])) {
            return new JsonResponse(['error' => 'Données invalides'], 400);
        }

        $user = $em->getRepository(User::class)->find($data['user_id']);
        $produit = $em->getRepository(Produit::class)->find($data['produit_id']);

        if (!$user || !$produit) {
            return new JsonResponse(['error' => 'Introuvable'], 404);
        }

        $sac = $em->getRepository(Sac::class)->findOneBy([
            'user' => $user,
            'produit' => $produit
        ]);

        if (!$sac) {
            return new JsonResponse(['error' => 'Produit non présent dans le sac'], 404);
        }

        $quantite = (int) $data['quantite'];

        if ($quantite <= 0) {
            return new JsonResponse(['error' => 'Quantité invalide'], 400);
        }

        $nouvelleQuantite = $sac->getQuantite() - $quantite;

        if ($nouvelleQuantite <= 0) {
            $em->remove($sac);
        } else {
            $sac->setQuantite($nouvelleQuantite);
        }

        $em->flush();

        return new JsonResponse([
            'message' => 'Produit retiré du sac'
        ]);
    }

    /**
     * VOIR SAC D'UN LIVREUR
     */
    #[Route('/{id}', methods: ['GET'])]
    public function getSac(
        int $userId,
        EntityManagerInterface $em
    ): JsonResponse {

        $user = $em->getRepository(User::class)->find($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'User introuvable'], 404);
        }

        $sacs = $em->getRepository(Sac::class)->findBy([
            'user' => $user
        ]);

        $data = [];

        foreach ($sacs as $sac) {
            $data[] = [
                'produit' => [
                    'id' => $sac->getProduit()?->getId(),
                    'nom' => $sac->getProduit()?->getNom(),
                ],
                'quantite' => $sac->getQuantite()
            ];
        }

        return new JsonResponse([
            'user' => $user->getUsername(),
            'sac' => $data
        ]);
    }
}