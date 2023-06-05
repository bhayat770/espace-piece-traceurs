<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Search;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\ProductImage;
use App\Entity\Tag;
use App\Form\SearchType;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use App\Service\BreadcrumbService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Config\Framework\Cache\PoolConfig;

class ProductController extends AbstractController
{
    private $entityManager;
    private $cart;

    public function __construct(EntityManagerInterface $entityManager, Cart $cart, BreadcrumbService $breadcrumbService) {
        $this->entityManager=$entityManager;
        $this->cart=$cart;
        $this->breadcrumbService = $breadcrumbService;
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


    private function getTagFromSlug(string $tagSlug, TagRepository $tagRepository, Cart $cart): ?Tag
    {
        // Supprimer les caractères spéciaux et remplacer les espaces par des tirets
        $tagSlug = preg_replace('/[^a-zA-Z0-9]+/', '-', trim(strtolower($tagSlug)));

        // Rechercher le tag correspondant au slug
        return $tagRepository->findOneBy(['slug' => $tagSlug]);
    }

    #[Route('/nos-produits/{categoryName}/{tags}', name: 'app_products_by_tag')]
    public function listByTag(TagRepository $tagRepository, string $slug, Cart $cart): Response
    {
        $tag = $tagRepository->findOneBy(['slug' => $slug]);
        if (!$tag) {
            throw $this->createNotFoundException('Tag not found');
        }

        $products = $tagRepository->findByTag($tag->getNom());


        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'tag' => $tag,
            'cart'=> $cart->getFull()
        ]);
    }


    #[Route('/produit/{slug}', name: 'app_product')]
    public function show($slug, Cart $cart, Product $product): Response
    {

        $maxQuantity = $product->getQuantite();

        // Récupérer le produit à afficher à partir de son slug
        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);

        // Si le produit n'existe pas, Rediriger vers la page des produits
        if (!$product)
        {
            return $this->redirectToRoute('app_products');
        }


        $firstImage = $product->getProductImages()->first();
        $breadcrumb = $this->breadcrumbService->generateBreadcrumb([
            'slug' => $slug,
            'pageName' => 'Product Page',
        ]);



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
            'firstImage'=> $firstImage,
            'maxQuantity' => $maxQuantity,
            'breadcrumb' => $breadcrumb
            ]);
    }



    #[Route("/nos-produits/{categoryName}", name: "app_products_by_category")]
    public function productsByCategory(CategoryRepository $categoryRepository, string $categoryName, Request $request, Cart $cart)
    {
        $category = $categoryRepository->findOneByName($categoryName);

        if (!$category) {
            throw $this->createNotFoundException('La catégorie n\'existe pas');
        }

        $products = $category->getProducts();

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('product/products_by_category.html.twig', [
            'category' => $category,
            'products' => $products,
            'categoryName' => $categoryName,
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart'=>$cart->getFull(),
        ]);
    }
}
