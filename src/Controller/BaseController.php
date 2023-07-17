<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function renderBaseTemplate(Request $request)
    {
        // Récupérer le nom de la catégorie à partir de la requête
        $categoryName = $request->query->get('category');

        // Recherchez la catégorie dans la base de données en fonction du nom
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $categoryName]);

        // Vérifiez si la catégorie existe
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        return $this->render('base.html.twig', [
            'category' => $category,
        ]);
    }
}
