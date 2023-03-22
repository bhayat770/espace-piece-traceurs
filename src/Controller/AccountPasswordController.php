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
        $form= $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $old_pwd = $form->get('old_password')->getData();
            if ($passwordHasher->isPasswordValid($user, $old_pwd)) {
                $new_pwd = $form->get('new_password')->getData();
                $password = $passwordHasher->hashPassword($user, $new_pwd);

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
        ]);

    }
}
