<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Traceurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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

    #[Route('/nos-magasins', name: 'app_stores')]
    public function stores(Cart $cart): Response
    {
        $traceurs = $this->entityManager->getRepository(Traceurs::class)->createQueryBuilder('t')
            ->where('t.nom LIKE :nom')
            ->setParameter('nom', '%DesignJet%')
            ->getQuery()
            ->getResult();

        // Vous pouvez ajouter ici des informations sur le magasin si nécessaire
        // Par exemple, les coordonnées pour la carte Google Maps
        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();
        return $this->render('static/magasins.html.twig', [
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart'=>$cart->getFull(),
            'traceurs'=>$traceurs
        ]);
    }

    #[Route('/faq', name: 'app_faq')]
    public function faq(Cart $cart): Response
    {
        // Vous pouvez ajouter ici des informations sur le magasin si nécessaire
        // Par exemple, les coordonnées pour la carte Google Maps
        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('static/faq.html.twig', [
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart'=>$cart->getFull(),
        ]);
    }

    #[Route('/nous-contacter', name: 'contact')]
    public function contact(Cart $cart): Response
    {

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();


        // Ajoutez ici le code de traitement du formulaire de contact si nécessaire

        return $this->render('static/contact.html.twig', [
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart'=>$cart->getFull(),
        ]);
    }


}
