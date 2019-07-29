<?php

namespace App\Form;

use App\Entity\SubInscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class SubInscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('attr' => array('placeholder' => 'Nombre Completo', 'length' => 30),
                'constraints' => array(
                    new NotBlank(array("message" => "Ingresa tu Nombre Completo")),
                )
            ))
            ->add('lastname', HiddenType::class, ['mapped' => false])
            ->add('identity', TextType::class, array('attr' => array('placeholder' => 'Cédula de Indentidad', 'length' => 30),
                'constraints' => array(
                    new NotBlank(array("message" => "Ingresa tu Cédula de Indentidad")),
                )
            ))
            ->add('address', TextType::class, array('attr' => array('placeholder' => 'Dirección Personal', 'length' => 255),
                'constraints' => array(
                    new NotBlank(array("message" => "Ingresa tu Dirección Personal")),
                )
            ))
            ->add('email', EmailType::class, array('attr' => array('placeholder' => 'Email'),
                'constraints' => array(
                    new NotBlank(array("message" => "Ingresa un email")),
                    new Email(array("message" => "Ingresa un email valido")),
                )
            ))
            ->add('level', ChoiceType::class, [
                'choices' => [
                    'Nivel 1' => SubInscription::LEVEL_ONE,
                    'Nivel 2' => SubInscription::LEVEL_TWO,
                    'Nivel 3' => SubInscription::LEVEL_THREE,
                ]
            ])
            ->add('startdate', DateType::class, [
                // renders it as a single text box
                'widget' => 'single_text',
                'attr' => array('placeholder' => 'Fecha')
            ])
            ->add('payment', FileType::class, array('multiple' => false, 'attr' => array('placeholder' => 'Talón de pago'),
                'constraints' => array(
                    new NotBlank(array("message" => "El pago es requerido")),
                )
            ))
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
