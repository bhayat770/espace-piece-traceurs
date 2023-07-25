<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Wishlist;
use App\Repository\ProductRepository;
use App\Repository\WishlistRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\String\u;

class WishlistController extends AbstractController
{

    private $wishlistRepository;

    public function __construct(WishlistRepository $wishlistRepository, EntityManagerInterface $entityManager)
    {
        $this->wishlistRepository = $wishlistRepository;
        $this->entityManager = $entityManager;

    }

    #[Route('/wishlist', name: 'app_wishlist')]
    public function viewWishlist(WishlistRepository $wishlistRepository, Cart $cart, ProductRepository $productRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            // User not authenticated, handle accordingly (e.g., redirect to login)
            // For example:
            return $this->redirectToRoute('app_login');
        }

        // Fetch the Wishlist entity for the current user
        $wishlist = $wishlistRepository->getWishlistByUser($user);

        if (!$wishlist) {
            // If the wishlist does not exist for the current user, you can handle it accordingly,
            // for example, redirect to a page or display a message.
            return $this->redirectToRoute('app_products');
        }

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('wishlist/show.html.twig', [
            'wishlist' => $wishlist,
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart' => $cart->getFull(),
        ]);
    }


    #[Route('/wishlist/add/{productId}', name: 'app_wishlist_add')]
    public function addProductToWishlist(int $productId, ProductRepository $productRepository): RedirectResponse
    {
        // Récupérez le produit à partir de son ID
        $product = $productRepository->find($productId);

        // Vérifiez si le produit existe
        if (!$product) {
            throw $this->createNotFoundException('Le produit avec l\'ID ' . $productId . ' n\'existe pas.');
        }

        // Récupérez l'utilisateur actuellement connecté (vous pouvez le faire de différentes manières selon votre système d'authentification)
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter un produit à votre wishlist.');
        }
        // Créez une nouvelle wishlist pour l'utilisateur s'il n'en a pas encore
        if ($user->getWishlists()->isEmpty()) {
            $wishlist = new Wishlist();
            $user->addWishlist($wishlist);
        }


        // Ajoutez le produit à la wishlist de l'utilisateur
        $user->getWishlists()->first()->addProduct($product);

        // Enregistrez les modifications dans la base de données
        $entityManager = $this->entityManager;
        $entityManager->flush();

        // Redirigez l'utilisateur vers la page de la wishlist (ou une autre page de votre choix)
        return $this->redirectToRoute('app_wishlist');
    }

    #[Route('/wishlist/remove/{productId}', name: 'app_wishlist_remove')]
    public function removeFromWishlist(int $productId, WishlistRepository $wishlistRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour gérer votre wishlist.');
        }

        // Récupérez la wishlist de l'utilisateur
        $wishlist = $wishlistRepository->getWishlistByUser($user);

        if (!$wishlist) {
            throw $this->createNotFoundException('Wishlist non trouvée.');
        }

        // Récupérez le produit à supprimer de la wishlist
        $product = $wishlist->getProductById($productId);

        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé dans la wishlist.');
        }

        // Supprimez le produit de la wishlist de l'utilisateur
        $wishlist->removeProduct($product);

        // Enregistrez les modifications dans la base de données
        $entityManager = $this->entityManager;
        $entityManager->flush();

        // Redirigez l'utilisateur vers la page de la wishlist (ou une autre page de votre choix)
        return $this->redirectToRoute('app_wishlist');
    }
}