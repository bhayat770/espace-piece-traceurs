<?php

namespace App\Controller;

use App\Classe\Cart;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;



class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function index(Request $request, PersistenceManagerRegistry $doctrine, Cart $cart): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur connecté

        // Vérifier Si la méthode de la requête est POST
        if ($request->isMethod('POST')) {
            // Mettre à jour les informations de l'utilisateur avec les données de la requête
            $user->setFirstName($request->request->get('acc-name'));
            $user->setLastName($request->request->get('acc-lastname'));
            $user->setEmail($request->request->get('acc-email'));

            // Enregistrer les modifications dans la base de données
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Rediriger vers la page du compte
            return $this->redirectToRoute('app_account');
        }

        // Récupérer les informations du panier
        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        // Afficher la page du compte avec les informations de l'utilisateur et du panier
        return $this->render('account/index.html.twig', [
            'user' => $user,
            'request' => $request,
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart'=>$cart->getFull(),
        ]);
    }
}