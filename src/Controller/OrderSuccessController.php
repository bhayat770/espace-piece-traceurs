<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
{
    $this->entityManager = $entityManager;
}

    #[Route('/commande/merci/{stripeSessionId}', name: 'app_order_validate')]

    public function index($stripeSessionId, Cart $cart): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() != $this->getUser())
        {
            return $this->redirectToRoute('app_home');
        }


        $cart->remove();

        if (!$order->isIsPaid())
        {
            //Vider la session cart

            $cart->remove();

            //Modifier le statut isPaid de notre commande en mettant 1

            $order->setIsPaid(1);
            $this->entityManager->flush();
            //Envoyer maiul pour confirmer la commande
        }

        //afficher qq info de la commande de l'user


        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();


        return $this->render('order_success/index.html.twig', [
            'order' => $order,
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart'=>$cart->getFull(),
        ]);
    }
}
