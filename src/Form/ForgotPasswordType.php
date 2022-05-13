<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email', RepeatedType::class, [
            'type' => EmailType::class,
            'invalid_message' => "Les adresses email doivent être identiques.",
            'required' => true,
            'constraints' => [],
            'first_options' => [
                'label' => 'Saisir votre adresse e-mail'
            ],
            'second_options' => [
                'label' => 'Confirmez votre adresses email'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}