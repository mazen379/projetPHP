<?php

// src/Controller/CommandeController.php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface; // Importer correctement cette classe
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Commande;

class CommandeController extends AbstractController
{
    #[Route("/commande/{id}", name: "commande_detail")]
    public function commandeDetail(int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer la commande par ID
        $commande = $entityManager->getRepository(Commande::class)->find($id);

        // Si la commande n'existe pas, renvoyer une erreur
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        // Afficher la page de détail de la commande
        return $this->render('commande/detail.html.twig', [
            'commande' => $commande,
        ]);
    }
}
