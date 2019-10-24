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
            ->add('name', TextType::class, array(
                "label" => "Titre de l'evenement",
            ))
            ->add('dateDebut', DateType::class, array(
                "label" => "Date de début",
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/mm/yyyy',
                'attr' => ['class' => 'js-datepicker'],
            ))
            ->add('heureDebut', TimeType::class, array(
                "label" => "Heure de début",
                "mapped" => false,
                "minutes" => range(00, 50, 10),
            ))
            ->add('duration', IntegerType::class, array(
                "label" => "Durée (en heures)",
            ))
            ->add('dateCloture', DateType::class, array(
                "label" => "Date de fin d'inscription",
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/mm/yyyy',
                'attr' => ['class' => 'js-datepicker'],
            ))
            ->add('heureCloture', TimeType::class, array(
                "label" => "Heure de fin d'inscription",
                "mapped" => false,
                "minutes" => range(00, 50, 10),
            ))
            ->add('inscriptionsMax', IntegerType::class, array(
                "label" => "Nombre maximum d'inscrits",
            ))
            ->add('description', TextareaType::class, array(
                "label" => "Description",
                "required" => false,
            ))
            ->add('lieu', EntityType::class, array(
                "label" => "Lieu de l'événement",
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
