<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/', name: 'app_home')]
    public function index(Request $request, SessionInterface $session, Cart $cart): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findByIsBest(1);
        $promos = $this->entityManager->getRepository(Product::class)->findByEnPromo(1);
        $cartouches = $this->entityManager->getRepository(Product::class)->findByBestCartouches(1);

        $repository = $this->entityManager->getRepository(Product::class);
        // Récupérer les best sellers activés contenant le mot "hp"
        $bestSellersHP = $repository->findBy(['bestSellers' => true, 'name' => '%hp%']);

        // Récupérer le nom de la catégorie à partir de la requête
        $categoryName = $request->query->get('category');

        // Recherchez la catégorie dans la base de données en fonction du nom
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $categoryName]);

        // Vérifiez si la catégorie existe

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();



        return $this->render('home/index.html.twig',[
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart'=>$cart->getFull(),
            'products' => $products,
            'promos' => $promos,
            'cartouches'=>$cartouches,
            'category' => $category,
            'bestSellersHP'=>$bestSellersHP,
        ]);
    }
}
