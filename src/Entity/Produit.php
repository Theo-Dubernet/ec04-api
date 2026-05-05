<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $prix = null;

    /**
     * @var Collection<int, Sac>
     */
    #[ORM\OneToMany(targetEntity: Sac::class, mappedBy: 'produit')]
    private Collection $sacs;

    public function __construct()
    {
        $this->sacs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection<int, Sac>
     */
    public function getSacs(): Collection
    {
        return $this->sacs;
    }

    public function addSac(Sac $sac): static
    {
        if (!$this->sacs->contains($sac)) {
            $this->sacs->add($sac);
            $sac->setProduit($this);
        }

        return $this;
    }

    public function removeSac(Sac $sac): static
    {
        if ($this->sacs->removeElement($sac)) {
            if ($sac->getProduit() === $this) {
                $sac->setProduit(null);
            }
        }

        return $this;
    }
}
