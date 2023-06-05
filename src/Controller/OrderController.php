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

class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande', name: 'app_order')]
    public function index(Cart $cart, Request $request): Response
    {
        // Si l'utilisateur connecté n'a pas d'adresses enregistrée, rediriger vers la page d'ajout d'adresse
        if (!$this->getUser()->getAdresses()->getValues()) {
            return $this->redirectToRoute('app_account_adresse_add');
        }

        // Créer un formulaire pour la commande
        $form = $this->createForm(OrderType::class, null, [
            "user" => $this->getUser()
        ]);

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull(),
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts
        ]);
    }


    #[Route('/commande/recapitulatif', name: 'app_order_recap', methods: ['POST'])]
    public function add(Cart $cart, Request $request, PersistenceManagerRegistry $doctrine, $carriers = null): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            "user" => $this->getUser()
        ]);

        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, récupérer les données du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTimeImmutable();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('adresses')->getData();
            // Préparer le contenu de l'adresse de livraison
            $delivery_content = $delivery->getFirstName() . ' ' . $delivery->getLastName();
            $delivery_content .= '<br/>' . $delivery->getTelephone();

            //Si l'user à renseigné une société ...
            if ($delivery->getSociete()) {
                $delivery_content .= '<br/>' . $delivery->getSociete();
            }

            $delivery_content .= '<br/>' . $delivery->getAdresse();
            $delivery_content .= '<br/>' . $delivery->getPostal() . $delivery->getVille();
            $delivery_content .= '<br/>' . $delivery->getPays($delivery_content);

            // Enregistrer la commande dans la base de données
            $order = new Order();
            $reference = $date->format('dmY') . '-' . uniqid();
            $order->setReference($reference);
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrix());
            $order->setDelivery($delivery_content);
            $order->SetState(0);
            $this->entityManager->persist($order);


            // Enregistrer les produits de la commande dans la base de données
            foreach ($cart->getFull() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduit($product['product']->getName());
                $orderDetails->setQuantite($product['quantity']);
                if ($product['product']->getPrixPromo() !== null) {
                    $orderDetails->setPrix($product['product']->getPrixPromo());
                } else {
                    $orderDetails->setPrix($product['product']->getPrice());
                }
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);

                // Enregistrer les détails de la commande dans la base de données
                $this->entityManager->persist($orderDetails);

            }

            // Enregistrer la commande dans la base de données
            $this->entityManager->persist($order);
            $this->entityManager->flush();

            $cartTotal = $cart->getTotal();
            $cartProducts = $cart->getProducts();


            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                "delivery" => $delivery_content,
                'cartTotal' => $cartTotal,
                'cartProducts' => $cartProducts,
                'reference'=>$order->getReference()
            ]);
        }

        // Si le formulaire n'est pas soumis ou n'est pas valide, rediriger vers la page du panier
        return $this->redirectToRoute('app_cart');
    }
}