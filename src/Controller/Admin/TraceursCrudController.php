<?php

namespace App\Controller\Admin;

use App\Entity\Traceurs;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TraceursCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Traceurs::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('marque'),
            TextField::new('nom'),
            TextField::new('reference'),
           // TextEditorField::new('description'),
            ImageField::new('photo')
                ->setBasePath('/public/assets/images/hp')
                ->setUploadDir('public/assets/images/hp'),
            SlugField::new('slug')->setTargetFieldName('nom')->onlyOnForms(),

        ];
    }

}
