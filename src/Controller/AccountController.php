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
        $user = $this->getUser(); //Récupérer l'utilisateur connecté

        if ($request->isMethod('POST')) {
            $user->setFirstName($request->request->get('acc-name'));
            $user->setLastName($request->request->get('acc-lastname'));
            $user->setEmail($request->request->get('acc-email'));

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_account');
        }

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('account/index.html.twig', [
            'user'=>$user,
            'request' => $request,
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
        ]);
    }
}


