<?php

namespace App\Form;

use App\Entity\SubInscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubInscription1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('identity')
            ->add('address')
            ->add('email')
            ->add('level')
            ->add('startdate')
            ->add('payment')
            ->add('sections')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SubInscription::class,
        ]);
    }
}
