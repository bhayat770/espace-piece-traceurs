<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
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

        if ($order->getState() == 0)
        {
            //Vider la session cart

            $cart->remove();

            //Modifier le statut isPaid de notre commande en mettant 1

            $order->setState(1);
            $this->entityManager->flush();
            //Envoyer mail pour confirmer la commande

            $mail = new Mail();
            $content = "Bonjour, ".$order->getUser()->getFirstname()."<br/>Merci pour votre commande ".$stripeSessionId. ".<br><br> Voici la liste des produits commandés :<br>";
            foreach ($order->getOrderDetails() as $orderDetail) {
                $produit = $orderDetail->getProduit();
                $content .= "- ".$produit." (x".$orderDetail->getQuantite().")<br> ";
                $content .= '<img src="'.$orderDetail->getIllustration().'" alt="'.$orderDetail->getProduit().'"><br>';
            }
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Votre commande chez Inforiel a bien été validée !', $content);
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
