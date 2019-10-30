<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form use to manage User
 * Class UserType
 * @package App\Form
 */
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'label' => 'Pseudo',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('name', TextType::class, array(
                'label' => 'Nom',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('firstName', TextType::class, array(
                'label' => 'Prenom',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('phone',TelType::class, array(
                'label' => 'Numéro de télephone',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control'
                ),
            ))
            ->add('mail',EmailType::class, array(
                'label' => 'Adresse mail',
                'attr' => array(
                    'class' => 'form-control'
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
