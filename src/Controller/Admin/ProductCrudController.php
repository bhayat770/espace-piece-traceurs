<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Tag;
use App\Form\ProductImageType;
use App\Form\TagEntityType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldAbstractType;
use Doctrine\Common\Collections\Collection;



class ProductCrudController extends AbstractCrudController
{

    public const ACTION_DUPLICATE = 'duplicate';
    public const DETAIL = 'detail';
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }

    public function configureActions(Actions $actions): Actions
    {

        $duplicate = Action::new(self::ACTION_DUPLICATE)
            ->linkToCrudAction('duplicateProduct')
            ->setCssClass('btn btn-info');

        $details = Action::new(self::DETAIL)
            ->linkToCrudAction('detail')
            ->setCssClass('btn btn-info');

        $applyDiscount = Action::new('applyDiscount', 'Appliquer la réduction', 'fa fa-percent')
            ->linkToCrudAction('applyDiscount')
            ->setCssClass('btn btn-info');


        return $actions
            ->add(Crud::PAGE_EDIT, $duplicate)
            ->add('index', 'detail')
            ->add(Crud::PAGE_INDEX, $applyDiscount);



    }

    public function applyDiscount(AdminContext $context, EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        /** @var Product $product */
        $product = $context->getEntity()->getInstance();

        if ($product->getMarque() === 'HP') {
            // Calculer la réduction de 10%
            $newPrice = $product->getPrice() * 0.9;
            $product->setPrice($newPrice);
            $entityManager->persist($product);
            $entityManager->flush();
        }

        // Générer l'URL de redirection vers la page de liste des produits
        $url = $adminUrlGenerator->setController(ProductCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureFields(string $pageName): iterable
    {
        $weightChoices = [
            '0.25 kg' => '1',
            '0.5 kg' => '2',
            '1 kg' => '3',
            '2 kg' => '4',
            '3 kg' => '5',
            // Ajoutez les poids que vous voulez ici
        ];
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom'),
            SlugField::new('slug')->setTargetFieldName('name'),
            AssociationField::new('category', 'Catégorie')
                ->setQueryBuilder(function (QueryBuilder $queryBuilder) {
                    $queryBuilder->where('entity.active = true');
            }),
            AssociationField::new('marque'),
            TextField::new('partnumber', 'Numéro de référence'),
            TextField::new('subtitle', 'Sous-titre')->hideOnIndex(),
            TextField::new('illustrationFile')
                ->setFormType(VichImageType::class)
                ->onlyOnForms()
            ->hideOnForm(),
            ImageField::new('illustration')
                ->setBasePath('/public/assets/images/hp')
                ->setUploadDir('public/assets/images/hp'),
            MoneyField::new('price', 'Prix')->setCurrency('EUR'),
            TextEditorField::new('description')
            ->setFormType(CKEditorType::class),

            DateTimeField::new('updatedAt')->hideOnForm()->hideOnIndex(),
            DateTimeField::new('createdAt')->hideOnForm()->hideOnIndex(),
            BooleanField::new('active', 'Activé'),
            ChoiceField::new('poids', 'Poids')->setChoices($weightChoices),
            IntegerField::new('quantite', 'Quantité'),
            CollectionField::new('productImages')
                ->setEntryType(ProductImageType::class),
            AssociationField::new('tags')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->autocomplete(),
            BooleanField::new('isBest'),
            BooleanField::new('enPromo'),
            BooleanField::new('bestCartouches'),
            BooleanField::new('bestSellers'),
            MoneyField::new('prixPromo', 'Prix promotionnel')->setCurrency('EUR')->onlyOnForms(),

        ];
    }
/*
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Product) return;

        $entityInstance->setCreatedAt(new \DateTimeImmutable());

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Product) return;

        $entityInstance->setUpdatedAt(new \DateTimeImmutable());

        parent::persistEntity($entityManager, $entityInstance);
    } */

    public function duplicateProduct(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        /** @var @var $product */
        $product = $context->getEntity()->getInstance();

        $duplicatedProduct = clone $product;
        parent::persistEntity($entityManager, $duplicatedProduct);

        $url = $adminUrlGenerator->setController(self::class)
            ->setAction(Action::DETAIL)
            ->setEntityId($duplicatedProduct->getId())
            ->generateUrl();

        return $this->redirect($url);
    }
}
