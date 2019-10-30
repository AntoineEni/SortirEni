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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form use to manage User
 * Class UserType
 * @package App\Form
 */
class UserType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'label' => $this->translator->trans("form.user.username"),
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('name', TextType::class, array(
                'label' => $this->translator->trans("form.user.name"),
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('firstName', TextType::class, array(
                'label' => $this->translator->trans("form.user.firstname"),
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('phone',TelType::class, array(
                'label' => $this->translator->trans("form.user.phone"),
                'required' => false,
                'attr' => array(
                    'class' => 'form-control'
                ),
            ))
            ->add('mail',EmailType::class, array(
                'label' => $this->translator->trans("form.user.mail"),
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
