<?php

namespace App\Controller;
use App\Entity\Commande;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\PanierProduit;
use Symfony\Component\HttpFoundation\RedirectResponse;
class PanierController extends AbstractController
{
    #[Route('/panier/ajouter/{id}', name: 'ajouter_panier', methods: ['POST'])]
public function ajouterAuPanier(Produit $produit, EntityManagerInterface $entityManager): Response
{
    // Récupérer l'utilisateur connecté
    $utilisateur = $this->getUser();
    if (!$utilisateur) {
        return $this->json(['message' => 'Vous devez être connecté pour ajouter un produit au panier.'], 401);
    }

    // Vérifier si l'utilisateur a déjà un panier
    $panier = $entityManager->getRepository(Panier::class)->findOneBy(['utilisateur' => $utilisateur]);
    if (!$panier) {
        // Si aucun panier, en créer un
        $panier = new Panier();
        $panier->setUtilisateur($utilisateur);
        $entityManager->persist($panier);
    }

    // Vérifier si le produit existe déjà dans le panier via PanierProduit
    $panierProduit = $entityManager->getRepository(PanierProduit::class)->findOneBy([
        'panier' => $panier,
        'produit' => $produit,
    ]);

    if ($panierProduit) {
        // Si le produit existe déjà, augmenter la quantité
        $panierProduit->setQuantite($panierProduit->getQuantite() + 1);
    } else {
        // Si le produit n'existe pas encore, l'ajouter au panier
        $panierProduit = new PanierProduit($panier, $produit, 1);
        $entityManager->persist($panierProduit);
    }

    $entityManager->flush();

    return $this->render('panier/index.html.twig', [
        'panier' => $panier,
    ]);
}


    #[Route('/panier', name: 'voir_panier')]
    public function voirPanier(EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $this->getUser();
        if (!$utilisateur) {
            return $this->redirectToRoute('app_login');
        }

        $panier = $entityManager->getRepository(Panier::class)->findOneBy(['utilisateur' => $utilisateur]);

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
        ]);
    }
    #[Route("/panier/update", name: "update_panier", methods: ["POST"])]
    public function updatePanier(Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        // Vérifier si l'utilisateur est connecté
        $utilisateur = $this->getUser();

        if (!$utilisateur) {
            return $this->redirectToRoute('app_login'); // Rediriger si non connecté
        }

        // Récupérer le panier de l'utilisateur
        $panier = $entityManager->getRepository(Panier::class)->findOneBy(['utilisateur' => $utilisateur]);

        if (!$panier) {
            return $this->redirectToRoute('voir_panier'); // Rediriger si le panier n'existe pas
        }

        // Récupérer les quantités envoyées par le formulaire
        $quantites = $request->request->all('quantites'); // récupère un tableau des quantités

        // Mettre à jour les quantités dans le panier
        foreach ($panier->getPanierProduits() as $panierProduit) {
            $produitId = $panierProduit->getProduit()->getId();
            
            // Si une quantité a été définie pour ce produit, la mettre à jour
            if (isset($quantites[$produitId])) {
                $panierProduit->setQuantite((int) $quantites[$produitId]);
            }
        }

        // Calculer le total du panier
        $total = 0;
        foreach ($panier->getPanierProduits() as $panierProduit) {
            $total += $panierProduit->getProduit()->getPrix() * $panierProduit->getQuantite();
        }

        // Créer une nouvelle commande
        $commande = new Commande();
        $commande->setUtilisateur($utilisateur);
        $commande->setPanier($panier);
        $commande->setTotal($total);
        $commande->setDateCommande(new \DateTime());

        // Sauvegarder la commande dans la base de données
        $entityManager->persist($commande);
        $entityManager->flush();

        // Sauvegarder le panier mis à jour dans la base de données
        $entityManager->flush();

        // Rediriger vers la page de la commande ou le panier
        return $this->redirectToRoute('commande_detail', ['id' => $commande->getId()]);


    }
    
    #[Route("/panier/supprimer/{id}", name: "supprimer_produit_panier", methods: ["GET"])]
     
    public function supprimerProduit(Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $this->getUser();
        if (!$utilisateur) {
            return $this->redirectToRoute('app_login');
        }

        $panier = $entityManager->getRepository(Panier::class)->findOneBy(['utilisateur' => $utilisateur]);

        if ($panier) {
            $panierProduit = $entityManager->getRepository(PanierProduit::class)
                ->findOneBy(['panier' => $panier, 'produit' => $produit]);

            if ($panierProduit) {
                $panier->removePanierProduit($panierProduit);
                $entityManager->remove($panierProduit);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('voir_panier');
    }
}
