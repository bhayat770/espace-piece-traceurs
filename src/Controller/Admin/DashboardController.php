<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use App\Entity\Category;
use App\Entity\Marque;
use App\Entity\Product;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{


    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    ){}

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        $url = $this->adminUrlGenerator->setController(ProductCrudController::class)
            ->generateUrl();

        return $this->redirect($url);

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Inforiel ');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section("Utilisateurs");
        yield MenuItem::subMenu("Actions",'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Ajouter un utilisateur', 'fas fa-plus', User::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les utilisateurs', 'fas fa-eye', User::class)
        ]);
        yield MenuItem::section("Catégories");
        yield MenuItem::subMenu("Actions",'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Ajouter une catégorie', 'fas fa-plus', Category::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les catégories', 'fas fa-eye', Category::class)
        ]);

        yield MenuItem::section("Marques");
        yield MenuItem::subMenu("Actions",'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Ajouter une marque', 'fas fa-plus', Marque::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les marques', 'fas fa-eye', Marque::class)
        ]);

        yield MenuItem::section("Produits");
        yield MenuItem::subMenu("Actions",'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Ajouter un produit', 'fas fa-plus', Product::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les produits', 'fas fa-eye', Product::class)
        ]);

        yield MenuItem::section("Transporteurs");
        yield MenuItem::subMenu("Actions",'fas fa-truck')->setSubItems([
            MenuItem::linkToCrud('Ajouter un transporteur', 'fas fa-plus', Carrier::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les transporteurs', 'fas fa-eye', Carrier::class)
        ]);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
