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
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?Categorie $categorie = null;

    #[ORM\Column(length: 255)]
    private ?string $prix = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;



    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $fichier = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: FournisseurProduit::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $fournisseurProduits;

    public function __construct()
    {
        $this->fournisseurProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getFichier(): ?FichierAdmin
    {
        return $this->fichier;
    }

    public function setFichier(?FichierAdmin $fichier): self
    {
        $this->fichier = $fichier;

        return $this;
    }

    /**
     * @return Collection<int, FournisseurProduit>
     */
    public function getFournisseurProduits(): Collection
    {
        return $this->fournisseurProduits;
    }

    public function addFournisseurProduit(FournisseurProduit $fournisseurProduit): static
    {
        if (!$this->fournisseurProduits->contains($fournisseurProduit)) {
            $this->fournisseurProduits->add($fournisseurProduit);
            $fournisseurProduit->setProduit($this);
        }

        return $this;
    }

    public function removeFournisseurProduit(FournisseurProduit $fournisseurProduit): static
    {
        if ($this->fournisseurProduits->removeElement($fournisseurProduit)) {
            // set the owning side to null (unless already changed)
            if ($fournisseurProduit->getProduit() === $this) {
                $fournisseurProduit->setProduit(null);
            }
        }

        return $this;
    }
}
