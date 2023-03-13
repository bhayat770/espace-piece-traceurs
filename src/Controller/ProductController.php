<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager=$entityManager;
    }

    #[Route('/nos-produits', name: 'app_products')]
    public function index(Request $request): Response
    {
        //Filtrage

        //initialiser la classe search
        $search= new Search();
        //passée a la methode createForm
        $form = $this->createForm(SearchType::class, $search);

        //ecouter le form
        $form->handleRequest($request);

        //si soumis et valide:
        if ($form->isSubmitted() && $form->isValid()) {
            //On rentre dedans et on crée une nouvelle méthode findWithSearch qui va chercher les produits de cette requete
         $products =$this->entityManager->getRepository(Product::class)->findWithSearch($search);
        }
        else
        {
            $products = $this->entityManager->getRepository(Product::class)->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products'=>$products,
            'form'=>$form->createView()
        ]);
    }

    #[Route('/produit/{slug}', name: 'app_product')]
    public function show($slug): Response
    {


        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);

        if (!$product) {
            return $this->redirectToRoute('app_products');
        }

        return $this->render('product/show.html.twig', [
            'product'=>$product
        ]);
    }
}
