<?php

namespace App\Entity;

use App\Repository\SacRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SacRepository::class)]
class Sac
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sacs')]
    private ?Produit $produit = null;

    #[ORM\ManyToOne(inversedBy: 'sacs')]
    private ?User $users = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): static
    {
        $this->users = $users;

        return $this;
    }
}
