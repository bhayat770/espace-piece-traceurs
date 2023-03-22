<?php

namespace App\Controller;

use App\Classe\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SessionInterface $session, Cart $cart): Response
    {

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('home/index.html.twig',[

            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
        ]);
    }
}
