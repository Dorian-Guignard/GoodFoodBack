<?php

namespace App\Form;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Virtue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File as FileConstraint;

class VirtueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('nameImage', FileType::class, [
                'label' => 'Image (JPG, PNG, JPEG, GIF file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new FileConstraint([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPG, PNG, JPEG or GIF image',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Virtue::class,
        ]);
    }
}
