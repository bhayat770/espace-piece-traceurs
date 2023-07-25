<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => ['rows' => 5, 'class' => 'form-control'] // Ajoutez la classe CSS 'form-control' ici
            ])
            ->add('product', HiddenType::class, [])
            ->add('send', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => ['class' => 'btn btn-primary'] // Ajoutez la classe CSS 'btn btn-primary' ici
            ]);

        $builder->get('product')
            ->addModelTransformer(new CallbackTransformer(
                fn(Product $product) => $product->getId(),
                fn(Product $product) => $product->getName()
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'csrf_token_id' => 'comment-add'
        ]);
    }
}
