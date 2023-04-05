<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $entityManager;
    private $cart;

    public function __construct(EntityManagerInterface $entityManager, Cart $cart) {
        $this->entityManager=$entityManager;
        $this->cart=$cart;

    }

    #[Route('/nos-produits', name: 'app_products')]
    public function index(Request $request, Cart $cart): Response
    {
        //Filtrage

        //initialiser la classe search
        $search= new Search();
        //passée a la methode createForm
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        //Si soumis et valide:
        if ($form->isSubmitted() && $form->isValid()) {
            //On rentre dedans et on crée une nouvelle méthode findWithSearch qui va chercher les produits de cette requete
         $products =$this->entityManager->getRepository(Product::class)->findWithSearch($search);
        }
        else //Sinon on les affiche tous
        {
            $products = $this->entityManager->getRepository(Product::class)->findAll();
        }


        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();


        return $this->render('product/index.html.twig', [
            'products'=>$products,
            'form'=>$form->createView(),

            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart'=>$cart->getFull(),

        ]);
    }

    #[Route('/produit/{slug}', name: 'app_product')]
    public function show($slug, Cart $cart): Response
    {
        // Récupérer le produit à afficher à partir de son slug
        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);

        // Si le produit n'existe pas, Rediriger vers la page des produits
        if (!$product)
        {
            return $this->redirectToRoute('app_products');
        }



        // Ajouter le produit au panier en fonction de son id
        //$this->cart->add($product->getId());

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render
        ('product/show.html.twig', [
            'product'=>$product,
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart'=>$cart->getFull(),
            ]);
    }
}
