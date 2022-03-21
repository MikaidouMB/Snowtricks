<?php

namespace App\Form;

use App\Entity\Images;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('images', FileType::class, [
                    'label' => false,
                    'required' => false,
                ]
            )
            ->add('delete', ButtonType::class, [
                'label_html' => true,
                //'label' => '<i class="fas fa-times"></i>',
                'attr' => [
                    'data-action' => 'delete',
                    'data-target' => '#trick_images___name__',
                ],
            ])
           ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=> Images::class,
        ]);
    }
}