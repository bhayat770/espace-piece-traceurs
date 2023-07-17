<?php

namespace App\Form;

use App\Classe\Search;
use App\Entity\Category;
use App\Entity\Tag;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
//Création du form pour les entrées qui représentent les propriété du class search
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('string', TextType::class,
                [
                    'label' => false, //ou false
                    'required' => false,
                    'attr' =>
                        [
                            'placeholder' => 'Votre recherche',
                            'class' => 'form-control-sm'
                        ]
                ])
            ->add('categories', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Category::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => function ($category) {
                    return ' ' . $category->getName();
                },
                'choice_attr' => function ($category) {
                    return ['class' => 'category-choice'];
                }
            ])
            ->add('tags', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Tag::class,
                'multiple' => true,
                'expanded' => false, // Modifier à false pour utiliser un menu déroulant
                'choice_label' => function ($tag) {
                    return '  ' . $tag->getNom();
                },
                'attr' => [
                    'class' => 'form-select tag-choice' // Ajouter les classes CSS pour le style Bootstrap
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer',
                'attr' => [
                    'class' => 'btn-block btn-warning'
                ]
            ]);
    }

    //Je le lie a ma searchClasse
    public function configureOptions(OptionsResolver $resolver): void
    {
        // Définit les options par défaut pour le résolveur d'options
        $resolver->setDefaults([
            // Spécifie la classe de données utilisée par le formulaire
            'data_class' => Search::class,
            // Définit la méthode HTTP utilisée pour soumettre le formulaire
            'method'=> 'GET',
            // Désactive la protection CSRF pour ce formulaire
            'crsf_protection'=> false,
        ]);
    }


    public function getBlockPrefix()
    {
        return '';
    }



}