<?php

namespace App\Form;
use Symfony\Bridge\Doctrine\Form\Type\ArrayType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Virtue;
use App\Entity\Category;
use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('duration')
            ->add('heatTime')
            ->add('prepTime')
            ->add('portion')
            ->add('picture')
            ->add('virtue', EntityType::class, [
                'class' => Virtue::class,
                'choice_label' => 'name',
                
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
               
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
