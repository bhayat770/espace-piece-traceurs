<?php

// src/Controller/CommandeController.php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SuiviCommandeController extends AbstractController
{
    private $entityManager;
    private $authorizationChecker;


    public function __construct(EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->entityManager = $entityManager;
    }

    #[Route('/suivi-commande', name: 'suivi_commande')]

    public function index(Request $request, Cart $cart)
    {

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();


// Récupération des données du formulaire
        $orderNumber = $request->query->get('numero');
        $name = $request->query->get('nom');

// Recherche de la commande dans la base de données
        $order = $this->entityManager
            ->getRepository(Order::class)
            ->findOneByNumberAndName($orderNumber, $name);




        if ($order) {
            // Redirection vers la page de détails de la commande
            return $this->redirectToRoute('app_account_order_show', [
                'reference' => $order->getReference(),
                'cartTotal' => $cartTotal,
                'cartProducts' => $cartProducts,
                'cart' => $cart->getFull(),
                'order' => $order, // Ajoutez cette ligne pour passer la variable $order à la vue
            ]);
        }

        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            // Autoriser l'accès aux utilisateurs non connectés
            return $this->render('suivi_commande/index.html.twig', [
                'cartTotal' => $cartTotal,
                'cartProducts' => $cartProducts,
                'cart' => $cart->getFull(),
                'order' => $order, // Assurez-vous que la variable $order est passée au modèle
            ]);
        }

        // Affichage du formulaire de suivi de commande
        return $this->render('suivi_commande/index.html.twig', [
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart' => $cart->getFull(),
            'order' => $order, // Ajoutez cette ligne pour passer la variable $order à la vue

        ]);
    }
}
