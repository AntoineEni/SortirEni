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

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class,[
                'label' => 'Pseudo',
                'attr' => ['class'=>'form-control']
            ])
            ->add('password',  RepeatedType::class, [
                'type' =>  PasswordType::class,
                'required' => false,
                'empty_data'=>'',
                'invalid_message' => "Vous n'avez pas saisi le même mot de passe",
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => ['class'=>'form-control']
                ],
                'second_options' => [
                    'label' => 'Confirmation du mot de passe',
                    'attr' => ['class'=>'form-control']
                ]
            ])
            ->add('name', TextType::class,[
                'label' => 'Nom',
                'attr' => ['class'=>'form-control']
            ])
            ->add('firstName', TextType::class,[
                'label' => 'Prenom',
                'attr' => ['class'=>'form-control']
            ])
            ->add('phone',TelType::class,[
                'label' => 'Numéro de télephone',
                'required' => false,
                'attr' => ['class'=>'form-control']
            ])
            ->add('mail',EmailType::class,[
                'label' => 'Adresse mail',
                'attr' => ['class'=>'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
