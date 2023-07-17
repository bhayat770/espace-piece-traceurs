<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Search;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\ProductImage;
use App\Entity\Tag;
use App\Form\CommentType;
use App\Form\SearchType;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ProductController extends AbstractController
{
    private $entityManager;
    private $cart;

    public function __construct(EntityManagerInterface $entityManager, Cart $cart)
    {
        $this->entityManager = $entityManager;
        $this->cart = $cart;
    }

    #[Route('/nos-produits', name: 'app_products')]
    public function index(Request $request, Cart $cart): Response
    {
        // Filtrage

        // Initialiser la classe Search
        $search = new Search();
        // Passée à la méthode createForm
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        // Si soumis et valide :
        if ($form->isSubmitted() && $form->isValid()) {
            // On rentre dedans et on crée une nouvelle méthode findWithSearch qui va chercher les produits de cette requête
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
        } else {
            // Sinon on les affiche tous
            $products = $this->entityManager->getRepository(Product::class)->findAll();
        }

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart' => $cart->getFull(),
        ]);
    }




    #[Route('/produit/{slug}', name: 'app_product')]
    public function show($slug, Cart $cart, Product $product, Security $security): Response
    {
        $maxQuantity = $product->getQuantite();

        // Récupérer le produit à afficher à partir de son slug
        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);

        // Si le produit n'existe pas, Rediriger vers la page des produits
        if (!$product) {
            return $this->redirectToRoute('app_products');
        }

        $firstImage = $product->getProductImages()->first();

        // Récupérer la catégorie associée au produit
        $tags = $product->getTags();

        // Récupérer les produits similaires en fonction de la catégorie du produit actuel
        $similarProducts = $this->entityManager->getRepository(Product::class)->findSimilarProductsByTags($tags, $product);

// Récupérer l'utilisateur actuellement connecté
        $user = $security->getUser();
// Créer un nouvel objet Comment et associer l'utilisateur
        $comment = new Comment($product);
        $comment->setUser($user);
     //  dd($user);

// Créer le formulaire de commentaire
        $commentForm = $this->createForm(CommentType::class, $comment);

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->renderForm('product/show.html.twig', [
            'product' => $product,
            'tags' => $tags,
            'similarProducts' => $similarProducts, // Ajout de la variable "similarProducts"
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart' => $cart->getFull(),
            'firstImage' => $firstImage,
            'maxQuantity' => $maxQuantity,
            'commentForm' => $commentForm
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

        // Fil d'Ariane
        $breadcrumb = [
            ['name' => 'Nos Produits', 'url' => $this->generateUrl('app_products')],
            ['name' => $category->getName(), 'url' => $this->generateUrl('app_products_by_category', ['categoryName' => $category->getName()])]
        ];

        return $this->render('product/products_by_category.html.twig', [
            'category' => $category,
            'products' => $products,
            'categoryName' => $categoryName,
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart' => $cart->getFull(),
            'breadcrumb' => $breadcrumb,
        ]);
    }

    #[Route('/nos-produits/{categoryName}/{tagName}', name: 'app_products_by_tag')]
    public function listByTag(TagRepository $tagRepository, CategoryRepository $categoryRepository, string $categoryName, string $tagName, Cart $cart): Response
    {
        // Récupérer le tag à partir du nom
        $tag = $tagRepository->findOneBy(['nom' => $tagName]);
        if (!$tag) {
            throw $this->createNotFoundException('Tag not found');
        }

        // Récupérer la catégorie à partir du nom
        $category = $categoryRepository->findOneBy(['name' => $categoryName]);
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        // Récupérer les produits associés au tag et à la catégorie
        $products = $category->getProductsByTag($tag);

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('product/by_tags.html.twig', [
            'products' => $products,
            'category' => $category,
            'tag' => $tag,
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart' => $cart->getFull(),
        ]);
    }

}
