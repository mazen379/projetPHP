<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    // Relation avec le panier
    #[ORM\ManyToOne(targetEntity: Panier::class, inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Panier $panier = null;

    // Total de la commande
    #[ORM\Column(type: 'float')]
    private ?float $total = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateCommande = null;

    public function __construct()
    {
        // Initialiser la collection de produits si nécessaire
        $this->dateCommande = new \DateTime();
        $this->total = 0.0; // Initialiser à 0 ou à une autre valeur par défaut
    }

    // Getters et setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): self
    {
        $this->panier = $panier;
        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): self
    {
        $this->dateCommande = $dateCommande;
        return $this;
    }

    // Méthode pour calculer le total de la commande en fonction des produits du panier
    public function calculerTotal(): void
    {
        $total = 0;

        // Vous devez parcourir les produits du panier pour calculer le total
        foreach ($this->panier->getPanierProduits() as $panierProduit) {
            $total += $panierProduit->getProduit()->getPrix() * $panierProduit->getQuantite();
        }

        // Mettre à jour le total
        $this->total = $total;
    }
}
