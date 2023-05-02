<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class Cart
{

    private $requestStack;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager ,RequestStack $requestStack)
    {
        $this->requestStack=$requestStack;
        $this->entityManager=$entityManager;
        $total = 0;
    }

    public function add($id)
    {
        // Récupérer la requête en cours
        $request = $this->requestStack->getCurrentRequest();
        // Récupérer la session
        $session = $request->getSession();
        // Récupérer le panier de la session ou un tableau vide si le panier n'existe pas
        $cart = $session->get('cart', []);

        // Si le produit est déjà dans le panier
        if (!empty($cart[$id])) {
            // Augmenter la quantité du produit dans le panier
            $cart[$id]++;
        }
        else {
            // Ajouter le produit au panier avec une quantité de 1
            $cart[$id] = 1;
        }

        // Enregistrer le panier dans la session
        $session->set('cart', $cart);
    }

    public function get()
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        return $session->get('cart');
    }

    public function remove()
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        $session->remove('cart');
    }

    public function delete($id)
    {
        $cart = $this->requestStack->getCurrentRequest();
        $session = $cart->getSession();
        $cart = $session->get('cart', []);

        unset($cart[$id]);

        return  $session->set('cart', $cart);
    }

    public function decrease($id)
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        // Récupérer le panier de la session ou un tableau vide si le panier n'existe pas
        $cart = $session->get('cart', []);

        // Si la quantité du produit dans le panier est supérieure à 1, diminuer la quantité du produit dans le panier
        if ($cart[$id]>1)
        {
            $cart[$id]--;
        }
        else
        {
            // Sinon, supprimer le produit du panier
            unset($cart[$id]);
        }
        return  $session->set('cart', $cart);
    }

    public function getProducts(): int
    {
        // Calculer la quantité totale de produits dans le panier
        $totalQuantity = 0;
        foreach ($this->getFull() as $product) {
            $totalQuantity += $product['quantity'];
        }
        return $totalQuantity;
    }

    public function getTotal(): float
    {
        // Calculer le prix total du panier
        $totalPrice = 0;
        foreach ($this->getFull() as $product)
        {
            $totalPrice += $product['product']->getPrice() * $product['quantity']/100;
        }

        return $totalPrice;
    }


    public function getFull()
    {
        // initialiser un tableau pour stocker les informations complètes du panier
        $cartComplete=[];

        // Si le panier existe
        if ($this->get())
        {
            // Pour chaque produit dans le panier, récupérer les informations du produit à partir de son identifiant
            foreach ($this->get() as $id =>$quantity)
            {

                $productObject =$this->entityManager->getRepository(Product::class)->findOneById($id);
                // Si le produit n'existe pas
                if (!$productObject)
                {
                    // Supprimer le produit du panier
                    $this->delete($id);
                    continue;
                }
                // Ajouter les informations du produit et sa quantité dans le tableau du panier complet
                $cartComplete[] =
                    [
                    'product' => $productObject,
                    'quantity' =>$quantity
                    ];
            }
        }

        // Renvoyer les informations complètes du panier
        return $cartComplete;
    }

    // Dans la classe Cart

    public function hasProduct($id)
    {
        return isset($this->products[$id]);
    }
    public function updateTotal(): void
    {
        $total = 0;

        foreach ($this->products as $product) {
            $total += $product['product']->getPrice() * $product['quantity'];
        }

        $this->total = $total;
    }

    public function increase($id, $quantity)
    {
        $this->products[$id]['quantity'] += $quantity;
        $this->updateTotal();
    }

}
