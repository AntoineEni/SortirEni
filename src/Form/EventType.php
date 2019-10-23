<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('dateDebut', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/mm/yyyy',
                'attr' => ['class' => 'js-datepicker'],
            ))
            ->add('heureDebut', TimeType::class, array(
                "mapped" => false,
                "minutes" => range(00, 50, 10),
            ))
            ->add('duration', IntegerType::class)
            ->add('dateCloture', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/mm/yyyy',
                'attr' => ['class' => 'js-datepicker'],
            ))
            ->add('heureCloture', TimeType::class, array(
                "mapped" => false,
                "minutes" => range(00, 50, 10),
            ))
            ->add('inscriptionsMax', IntegerType::class)
            ->add('description', TextareaType::class, array(
                "required" => false,
            ))
            ->add('lieu', EntityType::class, array(
                'class' => 'App\Entity\Location',
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un lieu'
            ))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
