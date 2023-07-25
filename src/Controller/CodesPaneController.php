<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Traceurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CodesPaneController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/codes-pane', name: 'app_codes_pane')]
    public function codesPane(Request $request, Cart $cart): Response
    {
        $traceurRepository = $this->entityManager->getRepository(Traceurs::class);
        $marquesTraceur = $traceurRepository->getDistinctMarques();
        $marques = array_column($marquesTraceur, 'marque'); // Extraire les valeurs de la clé 'marque'

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('codes_pane/index.html.twig', [
            'marquesTraceur' => $marques, // Utiliser le tableau extrait
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart' => $cart->getFull(),
        ]);
    }


    #[Route('/codes/pane/{marque}', name: 'app_get_series')]

    public function getSeries(Request $request, $marque): JsonResponse
    {
        $traceurRepository = $this->entityManager->getRepository(Traceurs::class);
        $seriesTraceur = $traceurRepository->getDistinctSeriesByMarque($marque);

        return new JsonResponse($seriesTraceur);
    }

    #[Route('/codes/pane/{marque}/{serie}', name: 'app_get_modeles')]

    public function getModeles(Request $request, $marque, $serie): JsonResponse
    {
        $traceurRepository = $this->entityManager->getRepository(Traceurs::class);
        $modelesTraceur = $traceurRepository->getDistinctModelesByMarqueAndSerie($marque, $serie);

        return new JsonResponse($modelesTraceur);
    }

    #[Route('/codes/pane/{marque}/{serie}/{modele}', name: 'app_get_codes_panes')]

    public function getCodesPanes(Request $request, $marque, $serie, $modele): JsonResponse
    {
        $traceurRepository = $this->entityManager->getRepository(Traceurs::class);
        $codesPanes = $traceurRepository->getCodesPanesByMarqueSerieAndModele($marque, $serie, $modele);

        return new JsonResponse($codesPanes);
    }
}
