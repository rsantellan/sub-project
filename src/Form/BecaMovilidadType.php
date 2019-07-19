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
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class BecaMovilidadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('attr' => array('placeholder' => 'Nombre Completo', 'length' => 30),
                'constraints' => array(
                    new NotBlank(array("message" => "Ingresa tu Nombre Completo")),
                )
            ))
            ->add('lastname', HiddenType::class)
            ->add('email', EmailType::class, array('attr' => array('placeholder' => 'Email'),
                'constraints' => array(
                    new NotBlank(array("message" => "Ingresa un email")),
                    new Email(array("message" => "Ingresa un email valido")),
                )
            ))
            ->add('telephone', TextType::class, array('attr' => array('placeholder' => 'Teléfono/Celular'),
                'constraints' => array(
                    new NotBlank(array("message" => "Ingresa un Teléfono/Celular")),
                )
            ))
            ->add('institution', TextType::class, array('attr' => array('placeholder' => 'Institución'),
                'constraints' => array(
                    new NotBlank(array("message" => "Ingresa una Institución")),
                )
            ))
            ->add('program', TextType::class, array('attr' => array('placeholder' => 'Programa de Posgrado'),
                'constraints' => array(
                    new NotBlank(array("message" => "Ingresa un Programa de Posgrado")),
                )
            ))
            ->add('cv', FileType::class, array('multiple' => true, 'attr' => array('placeholder' => 'CV resumido'),
                'constraints' => array(
                    new NotBlank(array("message" => "CV resumido es requerido")),
                )
            ))
            ->add('scholarship', FileType::class, array('multiple' => true, 'attr' => array('placeholder' => 'Escolaridad de Posgrado'),
                'constraints' => array(
                    new NotBlank(array("message" => "Escolaridad de Posgrado es requerido")),
                )
            ))
            ->add('letter', FileType::class, array('multiple' => true, 'attr' => array('placeholder' => 'Carta aval orientador'),
                'constraints' => array(
                    new NotBlank(array("message" => "Carta aval orientador es requerido")),
                )
            ))
            ->add('message', TextareaType::class, array('attr' => array('placeholder' => 'Carta intención  (breve descripción del proyecto y avances, descripción de la actividad a ser financiada y  sus beneficios para el proyecto. Máximo 1000 palabras)'),
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
        return 'beca_movilidad_form';
    }
}