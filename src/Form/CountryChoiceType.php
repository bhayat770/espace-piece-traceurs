<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountryChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                'France' => 'fr',
                'United States' => 'us',
                // Ajoutez les autres pays avec leurs codes respectifs
            ],
            'choice_label' => function ($value, $key, $index) {
                return sprintf('<img src="https://www.countryflags.io/%s/flat/32.png" alt="%s"> %s', $value, $value, $key);
            },
            'choice_html' => true,
            'placeholder' => 'Choisissez votre pays',
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

}
