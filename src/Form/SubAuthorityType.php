<?php

namespace App\Form;

use App\Entity\SubAuthority;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubAuthorityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('title')
            ->add('email', EmailType::class)
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Titular' => SubAuthority::TITULAR,
                    'Suplente' => SubAuthority::SUPLENTE,
                    'Comisión' => SubAuthority::COMISION,
                ]
            ])
            ->add('enable')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SubAuthority::class,
        ]);
    }
}
