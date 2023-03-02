<?php

namespace App\Form;

use App\Entity\Food;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Composition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('unity')
            ->add('quantity')
            ->add('recipe', EntityType::class, [
                'class' => Recipe::class,
                'choice_label' => 'name',
                
            ])
            ->add('food', EntityType::class, [
                'class' => Food::class,
                'choice_label' => 'name',
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Composition::class,
        ]);
    }
}