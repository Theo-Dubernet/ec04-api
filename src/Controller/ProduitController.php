<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/produit')]
final class ProduitController extends AbstractController
{
    /**
     * AJOUTER PRODUIT
     */
    #[Route('', name: 'create_produit', methods: ['POST'])]
    public function createProduit(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom']) || !isset($data['prix'])) {
            return new JsonResponse(['error' => 'Données invalides'], 400);
        }

        $produit = new Produit();
        $produit->setNom($data['nom']);
        $produit->setPrix($data['prix']);

        $em->persist($produit);
        $em->flush();

        return new JsonResponse([
            'id' => $produit->getId(),
            'nom' => $produit->getNom(),
            'prix' => $produit->getPrix()
        ], 201);
    }

    /**
     * MODIFIER PRIX PRODUIT
     */
    #[Route('/{id}', name: 'update_produit_price', methods: ['PATCH'])]
    public function updateProduitPrice(
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $produit = $em->getRepository(Produit::class)->find($id);

        if (!$produit) {
            return new JsonResponse(['error' => 'Produit non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['prix'])) {
            return new JsonResponse(['error' => 'Prix requis'], 400);
        }

        $produit->setPrix($data['prix']);
        $em->flush();

        return new JsonResponse([
            'id' => $produit->getId(),
            'prix' => $produit->getPrix()
        ]);
    }

    /**
     * SUPPRIMER PRODUIT PAR ID
     */
    #[Route('/{id}', name: 'delete_produit', methods: ['DELETE'])]
    public function deleteProduit(
        int $id,
        EntityManagerInterface $em
    ): JsonResponse {
        $produit = $em->getRepository(Produit::class)->find($id);

        if (!$produit) {
            return new JsonResponse([
                'error' => 'Produit non trouvé'
            ], 404);
        }

        $em->remove($produit);
        $em->flush();

        return new JsonResponse(null, 204); // REST → No Content
    }

    /**
     * RECUP PRODUIT DETAIL
     */
    #[Route('/{id}', name: 'get_produit', methods: ['GET'])]
    public function getProduit(
        int $id,
        EntityManagerInterface $em
    ): JsonResponse {
        $produit = $em->getRepository(Produit::class)->find($id);

        if (!$produit) {
            return new JsonResponse(['error' => 'Produit non trouvé'], 404);
        }

        $sacs = [];
        foreach ($produit->getSacs() as $sac) {
            $sacs[] = [
                'id' => $sac->getId(),
                'quantite' => $sac->getQuantite(),
                'user_id' => $sac->getUser()?->getId()
            ];
        }

        return new JsonResponse([
            'id' => $produit->getId(),
            'nom' => $produit->getNom(),
            'prix' => $produit->getPrix(),
            'sacs' => $sacs
        ]);
    }

    /**
     * RECUP TOUS LES PRODUITS
     */
    #[Route('', name: 'list_produits', methods: ['GET'])]
    public function listProduits(EntityManagerInterface $em): JsonResponse
    {
        $produits = $em->getRepository(Produit::class)->findAll();

        $data = [];

        foreach ($produits as $produit) {
            $data[] = [
                'id' => $produit->getId(),
                'nom' => $produit->getNom(),
                'prix' => $produit->getPrix()
            ];
        }

        return new JsonResponse($data);
    }
}
