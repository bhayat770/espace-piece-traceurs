<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use App\Service\ShippingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/mon-panier', name: 'app_cart')]

    public function index(Cart $cart, SessionInterface $session, ShippingService $shippingService): Response
    {
        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('cart/index.html.twig', [
            'cart'=>$cart->getFull(),
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_add_to_cart')]
    public function add(Cart $cart, $id, Request $request): Response
    {

        $product = $this->entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Le produit n\'existe pas');
        }


        // On récupère la quantité à ajouter au panier
        $quantity = $request->request->getInt('quantity', $product->getQuantite());
        // On vérifie que la quantité demandée est valide
        if ($quantity <= 0 || $quantity > $product->getQuantite()) {
            $this->addFlash('warning', 'Le produit n\'est plus en stock');
            return $this->redirectToRoute('app_product', [
                'id' => $product->getId()
            ]);
        }


        // Si le produit est déjà dans le panier, on augmente simplement sa quantité
        if ($cart->hasProduct($id))
        {
            $cart->increase($id, $quantity);
        }
        else
        {
            // Sinon, on ajoute une nouvelle entrée au panier
            $cart->add($id, $quantity);
        }

        $this->addFlash('success', 'Le produit a bien été ajouté au panier');

        return $this->redirectToRoute('app_cart');
    }


    #[Route('/cart/remove', name: 'app_remove_my_cart')]

    public function remove(Cart $cart): Response
    {
        $cart->remove();

        return $this->redirectToRoute('app_products');
    }

    #[Route('/cart/delete{id}', name: 'app_delete_to_cart')]

    public function delete(Cart $cart, $id): Response
    {
        $cart->delete($id);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/decrease{id}', name: 'app_decrease_to_cart')]

    public function decrease(Cart $cart, $id): Response
    {
        $cart->decrease($id);

        return $this->redirectToRoute('app_cart');
    }


}
