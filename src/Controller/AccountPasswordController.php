<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class AccountPasswordController extends AbstractController
{
    #[Route('/compte/modifier-mon-mot-de-passe', name: 'app_account_password')]
    public function index(Request $request, PersistenceManagerRegistry $doctrine ,UserPasswordHasherInterface $passwordHasher, Cart $cart): Response
    {

        $user= $this->getUser();
        // Créer un formulaire pour changer le mot de passe
        $form= $this->createForm(ChangePasswordType::class, $user);

        // Traiter la requête du formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, récupérer l'ancien mot de passe saisi par l'utilisateur
        if ($form->isSubmitted() && $form->isValid()) {

            $old_pwd = $form->get('old_password')->getData();
            // Vérifier si l'ancien mot de passe est correct
            if ($passwordHasher->isPasswordValid($user, $old_pwd)) {
                // Récupérer le nouveau mot de passe saisi par l'utilisateur
                $new_pwd = $form->get('new_password')->getData();
                // Crypter le nouveau mot de passe
                $password = $passwordHasher->hashPassword($user, $new_pwd);

                // Mettre à jour le mot de passe de l'utilisateur dans la base de données
                $user->setPassword($password);
                $em = $doctrine->getManager();
                $em->persist($user);
                $em->flush();
            }
        }

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('account/password.html.twig',[
            'form'=>$form->createView(),
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart'=>$cart->getFull(),
        ]);
    }

}
