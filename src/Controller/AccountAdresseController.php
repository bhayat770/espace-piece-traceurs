<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Adresse;
use App\Form\AdresseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAdresseController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/adresses', name: 'app_account_adresse')]
    public function index(Cart $cart): Response
    {
        // Récupérer les informations du panier
        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();


        // Afficher la page des adresses avec les informations du panier
        return $this->render('account/adresse.html.twig', [
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart'=>$cart->getFull(),
        ]);
    }


    #[Route('/compte/ajouter-une-adresse', name: 'app_account_adresse_add')]
    public function add(Cart $cart, Request $request): Response
    {
        // Créer une nouvelle instance de la classe Adresse
        $adresse = new Adresse();

        // Créer un formulaire pour l'adresse
        $form = $this->createForm(AdresseType::class, $adresse);

        // Traiter la requête du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Associer l'adresse à l'utilisateur connecté
            $adresse->setUser($this->getUser());
            // Enregistrer l'adresse dans la base de données
            $this->entityManager->persist($adresse);
            $this->entityManager->flush();
            // Si le panier contient des produits, rediriger vers la page de commande
            if ($cart->get()) {
                return $this->redirectToRoute('app_order');
            } else  //Sinon, Rediriger vers la page des adresses
            {

                return $this->redirectToRoute('app_account_adresse');
            }
        }

        // Récupérer les informations du panier
        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        // Afficher la page du formulaire d'ajout d'adresse avec les informations du panier et du formulaire
        return $this->render('account/adresse_form.html.twig', [
            'form' => $form->createView(),
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart'=>$cart->getFull(),
        ]);
    }

    #[Route('/compte/modifier-une-adresse/{id}', name: 'app_account_adresse_edit')]
    public function edit(Request $request, $id, Cart $cart): Response
    {
        // Récupérer l'adresse à modifier à partir de son identifiant
        $adresse = $this->entityManager->getRepository(Adresse::class)->findOneBy(['id' => $id]);

        // Si l'adresse n'existe pas ou si elle n'appartient pas à l'utilisateur connecté, rediriger vers la page des adresses
        if (!$adresse || $adresse->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('app_account_adresse');
        }

        // Créer un formulaire pour l'adresse
        $form = $this->createForm(AdresseType::class, $adresse);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer les modifications dans la base de données et rediriger vers la page des adresses
            $this->entityManager->flush();

            return $this->redirectToRoute('app_account_adresse');
        }

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('account/adresse_form.html.twig', [
            'form' => $form->createView(),
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart'=>$cart->getFull(),
        ]);
    }

    #[Route('/compte/supprimer-une-adresse/{id}', name: 'app_account_adresse_delete')]
    public function delete($id): Response
    {
        // Récupérer l'adresse à supprimer à partir de son identifiant
        $adresse = $this->entityManager->getRepository(Adresse::class)->findOneBy(['id' => $id]);

        // Si l'adresse n'existe pas ou si elle n'appartient pas à l'utilisateur connecté, rediriger vers la page des adresses
        if (!$adresse || $adresse->getUser() !== $this->getUser()) {
            //
            return $this->redirectToRoute('app_account_adresse');
        }

        // Supprimer l'adresse de la base de données
        $this->entityManager->remove($adresse);
        $this->entityManager->flush();

        // Rediriger vers la page des adresses
        return $this->redirectToRoute('app_account_adresse');
    }
}