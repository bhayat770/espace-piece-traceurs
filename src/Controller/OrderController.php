<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Translation\t;
use App\Service\ShippingService;

class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager )
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande', name: 'app_order')]
    public function index(Cart $cart, Request $request): Response
    {
        if (!$this->getUser()->getAdresses()->getValues())
        {
           return $this->redirectToRoute('app_account_adresse_add');
        }
        $form = $this->createForm(OrderType::class, null, [
            "user" => $this->getUser()
        ]);
        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();


        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart'=> $cart->getFull(),
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'app_order_recap', methods: 'POST')]
    public function add(Cart $cart, Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            "user" => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTimeImmutable();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('adresses')->getData();
            $delivery_content = $delivery->getFirstName().' '.$delivery->getLastName();
            $delivery_content .= '<br/>' .$delivery->getTelephone();

            if ($delivery->getSociete())
            {
                $delivery_content .= '<br/>' .$delivery->getSociete();
            }

            $delivery_content .= '<br/>' .$delivery->getAdresse();
            $delivery_content .= '<br/>' .$delivery->getPostal().$delivery->getVille();
            $delivery_content .= '<br/>' .$delivery->getPays($delivery_content);


            //enregistrer la commande
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrix());

            $order->setDelivery($delivery_content);

            $order->SetIsPaid(0);

            $this->entityManager->persist($order);


            //enregistrer les produits

            foreach ($cart->getFull() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduit($product['product']->getName());
                $orderDetails->setQuantite($product['quantity']);
                $orderDetails->setPrix($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);


                $this->entityManager->persist($orderDetails);
                $cartTotal = $cart->getTotal();
                $cartProducts = $cart->getProducts();


                return $this->render('order/add.html.twig', [
                    'cart'=> $cart->getFull(),
                    'carrier'=>$carriers,
                    "delivery"=>$delivery_content,
                    'cartTotal' => $cartTotal,
                    'cartProducts' => $cartProducts
                ]);

            }

         //   $this->entityManager->flush();

        }
        return $this->redirectToRoute('app_cart');

    }

}