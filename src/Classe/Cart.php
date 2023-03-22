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
    }

    public function add($id)
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        }
        else {
            $cart[$id] = 1;
        }

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
        //Vérifier si la qte du produit est différente de 1
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        $cart = $session->get('cart', []);

        if ($cart[$id]>1) {
            //retirer une qte
            $cart[$id]--;

        }else {
            //supprimer le prouduit
            unset($cart[$id]);
        }

        return  $session->set('cart', $cart);
    }
    public function getProducts(): int
    {
        $totalQuantity = 0;
        foreach ($this->getFull() as $product) {
            $totalQuantity += $product['quantity'];
        }
        return $totalQuantity;
    }

    public function getTotal(): float
    {
        $totalPrice = 0;
        foreach ($this->getFull() as $product) {
            $totalPrice += $product['product']->getPrice() * $product['quantity']/100;
        }

        return $totalPrice;
    }



    public function getFull()
    {
        $cartComplete=[];

        if ($this->get())
        {
            foreach ($this->get() as $id =>$quantity)
            {
                $productObject =$this->entityManager->getRepository(Product::class)->findOneById($id);
                if (!$productObject)
                {
                    $this->delete($id);
                    continue;
                }
                $cartComplete[] = [
                    'product' => $productObject,
                    'quantity' =>$quantity
                ];
            }
        }

        return $cartComplete;
    }
}
