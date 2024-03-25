<?php

namespace App\Service;

use App\Entity\Produit;
use App\Repository\ProductRepository;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{

    protected $session;
    protected $productRepository;
    private $requestStack;


    public function __construct(SessionInterface $session, ProduitRepository $productRepository, RequestStack $requestStack)
    {
        $session->start();
        $this->session = $session;
        $this->productRepository = $productRepository;
        $this->requestStack = $requestStack;
    }

    public function add(int $id)
    {
        $panier = $this->session->get('panier', []);

        if (empty($panier[$id])) {
            $panier[$id] = 0;
        }

        $panier[$id]++;

        $this->session->set('panier', $panier);
    }

    public function remove(int $id)
    {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $this->session->set('panier', $panier);
    }

    public function getFullCart(): array
    {
        $panier = $this->session->get('panier', []);

        $panierWithData = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        return $panierWithData;
    }

    public function getTotal(): float
    {
        $panierWithData = $this->getFullCart();

        $total = 0;

        foreach ($panierWithData as $couple) {
            $total += $couple['product']->getPrice() * $couple['quantity'];
        }

        return $total;
    }
}
