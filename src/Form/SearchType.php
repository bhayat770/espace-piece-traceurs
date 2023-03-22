<?php

namespace App\Form;

use App\Classe\Search;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class SearchType extends AbstractType
{
//Création du form pour les entrées qui représentent les propriété du class search
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('string', TextType::class,
                [
                'label'=> false, //ou false
                'required'=>false,
                'attr'=>
                    [
                    'placeholder'=>'Votre recherche',
                    'class' => 'form-control-sm'
                    ]
                ])
        ->add('categories', EntityType::class, //Permet de lier une entrée du form avec une entité
            [
            'label' => false,
            'required'=>false,
            'class' => Category::class, //cette clé précise avec quelle classe faire le lien
            'multiple'=>true,
            'expanded' =>true
            ])

            ->add('submit', SubmitType::class, [
                'label'=>'Filtrer',
                'attr' => [
                    'class' => 'btn-block btn-warning'
                ]
            ])
        ;
    }

    //Je le lie a ma searchClasse
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method'=> 'GET',
            'crsf_protection'=> false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }



}