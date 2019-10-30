<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form use to manage
 * Class UserPasswordType
 * @package App\Form
 */
class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password',  RepeatedType::class, array(
                'type' =>  PasswordType::class,
                'empty_data' => '',
                'invalid_message' => "Vous n'avez pas saisi le même mot de passe",
                'first_options' => array(
                    'label' => 'Mot de passe',
                    'attr' => array(
                        'class' => 'form-control',
                    ),
                ),
                'second_options' => array(
                    'label' => 'Confirmation du mot de passe',
                    'attr' => array(
                        'class'=>'form-control',
                    ),
                ),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
