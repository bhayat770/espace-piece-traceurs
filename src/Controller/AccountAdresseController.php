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
    public function index(): Response
    {

        return $this->render('account/adresse.html.twig');
    }

    #[Route('/compte/ajouter-une-adresse', name: 'app_account_adresse_add')]
    public function add(Cart $cart, Request $request ): Response
    {
        $adresse = new Adresse();

        $form = $this->createForm(AdresseType::class, $adresse);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adresse->setUser($this->getUser());
            $this->entityManager->persist($adresse);
            $this->entityManager->flush();
            if($cart->get() )
            {
                return $this->redirectToRoute('app_order');
            }
            else
            {
                return $this->redirectToRoute('app_account_adresse');
            }
        }

        return $this->render('account/adresse_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/compte/modifier-une-adresse/{id}', name: 'app_account_adresse_edit')]
    public function edit(Request $request, $id ): Response
    {
        $adresse = $this->entityManager->getRepository(Adresse::class)->findOneBy(['id' => $id]);

        if(!$adresse || $adresse->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('app_account_adresse');
        }

        $form = $this->createForm(AdresseType::class, $adresse);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_account_adresse');
        }

        return $this->render('account/adresse_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/compte/supprimer-une-adresse/{id}', name: 'app_account_adresse_delete')]
    public function delete($id): Response
    {
        $adresse = $this->entityManager->getRepository(Adresse::class)->findOneBy(['id' => $id]);

        if (!$adresse || $adresse->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('app_account_adresse');
        }

        $this->entityManager->remove($adresse);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_account_adresse');
    }

}
