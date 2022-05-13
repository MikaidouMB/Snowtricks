<?php

namespace App\Form;

use App\Entity\Videos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name', TextType::class, [
                 'label' => 'url',
                 'required' => false,
                 'constraints' => [
                     new Regex([
                         'pattern' => '#^((?:https?:)?\/\/)?(?:www\.)?((?:youtube\.com|youtu\.be|dai\.ly|dailymotion\.com|vimeo\.com|player\.vimeo\.com))(\/(?:[\w\-]+\?v=|embed\/|video\/|embed\/video\/)?)([\w\-]+)(\S+)?$#',
                         'message' => 'Vous ne pouvez inserer des urls issues des plateformes youtube, dailymotion ou vimeo',
                     ]),
                 ],
             ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Videos::class,
        ]);
    }
}