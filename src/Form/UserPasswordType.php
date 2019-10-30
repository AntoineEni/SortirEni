<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form use to manage
 * Class UserPasswordType
 * @package App\Form
 */
class UserPasswordType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password',  RepeatedType::class, array(
                'type' =>  PasswordType::class,
                'empty_data' => '',
                'invalid_message' => $this->translator->trans("form.password.error"),
                'first_options' => array(
                    'label' => $this->translator->trans("form.password.first"),
                    'attr' => array(
                        'class' => 'form-control',
                    ),
                ),
                'second_options' => array(
                    'label' => $this->translator->trans("form.password.second"),
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
