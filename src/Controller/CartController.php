<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use App\Service\ShippingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/mon-panier', name: 'app_cart')]

    public function index(Cart $cart, SessionInterface $session, ShippingService $shippingService): Response
    {
        // Récupère le poids total du panier depuis la session
        $totalWeight = $session->get('totalWeight');

        // Récupère le pays de destination depuis la session (ou un formulaire)
        $pays = 'Italy';

        // Récupère les frais d'expédition pour le pays et le poids total
        $shippingPrice = $shippingService->getShippingPrice($pays, $totalWeight);
        return $this->render('cart/index.html.twig', [
            'shippingPrice' => $shippingPrice,
            'cart'=>$cart->getFull()
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_add_to_cart')]

    public function add(Cart $cart, $id): Response
    {
        $cart->add($id);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove', name: 'app_remove_my_cart')]

    public function remove(Cart $cart): Response
    {
        $cart->remove();

        return $this->redirectToRoute('app_products');
    }

    #[Route('/cart/delete{id}', name: 'app_delete_to_cart')]

    public function delete(Cart $cart, $id): Response
    {
        $cart->delete($id);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/decrease{id}', name: 'app_decrease_to_cart')]

    public function decrease(Cart $cart, $id): Response
    {
        $cart->decrease($id);

        return $this->redirectToRoute('app_cart');
    }
}
