<?php
/**
 * @copyright Copyright © 2019 Geocom. All rights reserved.
 * @author    Rodrigo Santellan <rsantellan@geocom.com.uy>
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('attr' => array('placeholder' => 'Nombre y Apellido', 'length' => 30),
                'constraints' => array(
                    new NotBlank(array("message" => "Ingresa tu nombre y apellido")),
                )
            ))
            ->add('lastname', HiddenType::class)
            ->add('subject', TextType::class, array('attr' => array('placeholder' => 'Asunto'),
                'constraints' => array(
                    new NotBlank(array("message" => "Ingresa un asunto")),
                )
            ))
            ->add('email', EmailType::class, array('attr' => array('placeholder' => 'Email'),
                'constraints' => array(
                    new NotBlank(array("message" => "Ingresa un email")),
                    new Email(array("message" => "Ingresa un email valido")),
                )
            ))
            ->add('message', TextareaType::class, array('attr' => array('placeholder' => 'Mensaje'),
                'constraints' => array(
                    new NotBlank(array("message" => "Ingresa un mensaje")),
                )
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'error_bubbling' => true
        ));
    }

    public function getName()
    {
        return 'contact_form';
    }
}