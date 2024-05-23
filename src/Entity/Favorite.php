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
    private ?UserFront $id_userfront = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $id_produit = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_fav = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUserfront(): ?UserFront
    {
        return $this->id_userfront;
    }

    public function setIdUserfront(?UserFront $id_userfront): static
    {
        $this->id_userfront = $id_userfront;

        return $this;
    }

    public function getIdProduit(): ?Produit
    {
        return $this->id_produit;
    }

    public function setIdProduit(?Produit $id_produit): static
    {
        $this->id_produit = $id_produit;

        return $this;
    }

    public function getDateFav(): ?\DateTimeInterface
    {
        return $this->date_fav;
    }

    public function setDateFav(\DateTimeInterface $date_fav): static
    {
        $this->date_fav = $date_fav;

        return $this;
    }
}
