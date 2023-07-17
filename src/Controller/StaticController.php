<?php

namespace App\Controller;

use App\Classe\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticController extends AbstractController
{
    #[Route('/a-propos', name: 'app_about')]
    public function about(Cart $cart): Response
    {
        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();


        return $this->render('static/about.html.twig', [
                'cartTotal' => $cartTotal,
                'cartProducts' => $cartProducts,
                'cart' => $cart->getFull(),
            ]
        );
    }

    #[Route('/politique-confidentialite', name: 'app_privacy_policy')]
    public function privacyPolicy(Cart $cart): Response
    {
        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();


        return $this->render('static/privacy_policy.html.twig', [
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart' => $cart->getFull(),

        ]);
    }

    #[Route('/conditions-generales-vente', name: 'app_terms_of_sale')]
    public function termsOfSale(Cart $cart): Response
    {
        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();


        return $this->render('static/terms_of_sale.html.twig', [
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart' => $cart->getFull(),

        ]);
    }
}
