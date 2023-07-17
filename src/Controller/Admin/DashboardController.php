<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Marque;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\ProductImage;
use App\Entity\Tag;
use App\Entity\Traceurs;
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
        yield MenuItem::linkToRoute('Aller sur le site', 'fa fa-undo', 'app_home');

        yield MenuItem::section("Utilisateurs");
        yield MenuItem::subMenu("Actions",'fas fa-user')->setSubItems([
            MenuItem::linkToCrud('Ajouter un utilisateur', 'fas fa-plus', User::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les utilisateurs', 'fas fa-eye', User::class)
        ]);
        yield MenuItem::section("Catégories");
        yield MenuItem::subMenu("Actions",'class="fa-solid fa-business-time')->setSubItems([
            MenuItem::linkToCrud('Ajouter une catégorie', 'fas fa-plus', Category::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les catégories', 'fas fa-eye', Category::class)
        ]);

        yield MenuItem::section("Marques");
        yield MenuItem::subMenu("Actions",'fas fa-copyright')->setSubItems([
            MenuItem::linkToCrud('Ajouter une marque', 'fas fa-plus', Marque::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les marques', 'fas fa-eye', Marque::class)
        ]);

        yield MenuItem::section("Produits");
        yield MenuItem::subMenu("Actions",'fa-solid fa-print')->setSubItems([
            MenuItem::linkToCrud('Ajouter un produit', 'fas fa-plus', Product::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les produits', 'fas fa-eye', Product::class)
        ]);

        yield MenuItem::section("Transporteurs");
        yield MenuItem::subMenu("Actions",'fas fa-truck')->setSubItems([
            MenuItem::linkToCrud('Ajouter un transporteur', 'fas fa-plus', Carrier::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les transporteurs', 'fas fa-eye', Carrier::class)
        ]);
        yield MenuItem::section("Commandes");
        yield MenuItem::subMenu("Actions",'fa-solid fa-cart-shopping')->setSubItems([
            MenuItem::linkToCrud('Ajouter un transporteur', 'fas fa-plus', Order::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les commandes', 'fas fa-eye', Order::class)
        ]);

        yield MenuItem::section("Images");
        yield MenuItem::subMenu("Actions",'fa-solid fa-cart-image')->setSubItems([
            MenuItem::linkToCrud('Ajouter une image', 'fas fa-plus', ProductImage::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les images', 'fas fa-eye', ProductImage::class)
        ]);

        yield MenuItem::section("Tags");
        yield MenuItem::subMenu("Actions",'fa-solid fa-cart-image')->setSubItems([
            MenuItem::linkToCrud('Ajouter un tag', 'fas fa-plus', Tag::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les tags', 'fas fa-eye', Tag::class)
        ]);
         yield MenuItem::linkToCrud('Commentaires', 'fas fa-comment', Comment::class);
         yield MenuItem::linkToCrud('Traceurs', 'fas fa-shopping', Traceurs::class);
    }
}
