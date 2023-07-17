<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use App\Entity\Traceurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TraceursController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
 /**   #[Route('/traceurs', name: 'app_traceurs')]
    public function index(Cart $cart): Response
    {
        $traceurs = $this->entityManager->getRepository(Traceurs::class)->findAll();

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('traceurs/index.html.twig', [
            'traceurs' => $traceurs,
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart' => $cart->getFull(),
        ]);
}
**/
    #[Route('/traceurs/{slug}', name: 'app_traceurs')]
    public function show(string $slug, Cart $cart): Response
    {
        $traceur = $this->entityManager->getRepository(Traceurs::class)->findOneBy(['slug' => $slug]);

        if (!$traceur) {
            throw $this->createNotFoundException('Traceur non trouvé');
        }
        // Récupérer tous les produits ayant comme tag le traceur actuel
      //
        $products = $this->entityManager->getRepository(Product::class)->findByTag($slug);


        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('traceurs/show.html.twig', [
            'traceur' => $traceur,
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart' => $cart->getFull(),
            'products' => $products,

        ]);
    }
}