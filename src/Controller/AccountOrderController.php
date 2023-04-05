<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/mes-commandes', name: 'app_account_order')]
    public function index(Cart $cart): Response
    {

        $orders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser());


        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('account/order.html.twig', [
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart' => $cart->getFull(),
            'orders' => $orders
        ]);
    }

    #[Route('/compte/mes-commandes/{reference}', name: 'app_account_order_show')]

    public function show(Cart $cart, $reference): Response
    {

        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);

        if (!$order || $order->getUser() !=$this->getUser())
            {
                return $this->redirectToRoute('app_account_order');
            }

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('account/order_show.html.twig', [
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart' => $cart->getFull(),
            'order' => $order
        ]);
    }

}
