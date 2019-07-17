<?php

namespace App\Form;

use App\Entity\News;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true
            ])
            ->add('subtitle', TextType::class, [
                'required' => true
            ])
            ->add('Description', TextareaType::class, [
                'required' => true
            ])
            ->add('body', TextareaType::class, [
                'required' => true
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Imagen Superior' => News::FULL_NEW,
                    'Imagen lateral' => News::SIDE_NEW
                ]
            ])
            //->add('slug')
            //->add('created_at')
            //->add('updated_at')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
