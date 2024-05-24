<?php

namespace App\Entity;

use App\Repository\FavoriteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
class Favorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?userFront $userFront = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFav = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserFront(): ?userFront
    {
        return $this->userFront;
    }

    public function setUserFront(?userFront $userFront): static
    {
        $this->userFront = $userFront;

        return $this;
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

    public function getDateFav(): ?\DateTimeInterface
    {
        return $this->dateFav;
    }

    public function setDateFav(\DateTimeInterface $dateFav): static
    {
        $this->dateFav = $dateFav;

        return $this;
    }
}
