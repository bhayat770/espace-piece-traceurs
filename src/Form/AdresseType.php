<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdresseType extends AbstractType
{



    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Quel nom souhaitez-vous donner à votre adresse ?',
                'attr' => [
                    'placeholder' => 'Nommez votre adresse'
                ]
            ])
            ->add('firstname',TextType::class, [
                'label' => 'Entrez votre prénom',
                'attr' => [
                    'placeholder' => 'Nommez votre adresse'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Entrez votre nom',
                'attr' => [
                    'placeholder' => 'Nommez votre adresse'
                ]
            ])
            ->add('societe', TextType::class, [
                'label' => 'Votre societé ',
                'required'=>false,
                'attr' => [
                    'placeholder' => '(Facultatif) Entrez le nom de votre société'
                ]
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Votre adresse',
                'attr' => [
                    'placeholder' => '9 rue Lamartine...'
                ]
            ])
            ->add('postal', TextType::class, [
                'label' => 'Votre code postal',
                'attr' => [
                    'placeholder' => 'Entre votre code postal'
                ]
            ])
            ->add('ville', TextType::class, [
                'label' => 'Votre ville',
                'attr' => [
                    'placeholder' => 'Entrez votre ville'
                ]
            ])
            ->add('pays', CountryType::class, [
                'label' => 'Pays',
                'placeholder' => 'Choisissez votre pays',
                'required' => true,
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Entrez votre téléphone'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label'=> 'Valider',
                'attr' => [
                    'class' => 'btn-block btn-info',
                ]
            ])
        ;
    }
}
