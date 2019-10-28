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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                "label" => "Titre de l'evenement",
                'attr' => ['class' => 'form-control'],
                'label_attr'=>['class'=>'']
            ))
            ->add('dateDebut', DateType::class, array(
                "label" => "Date de début",
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => ['class' => 'js-datepicker form-control'],
                'label_attr'=>['class'=>'']
            ))
            ->add('heureDebut', TimeType::class, array(
                "label" => "as",
                "mapped" => false,
                "minutes" => range(00, 50, 10),
                'label_attr'=>['class'=>''],

            ))
            ->add('duration', IntegerType::class, array(
                "label" => "Durée (en heures)",
                'label_attr'=>['class'=>''],
            ))
            ->add('dateCloture', DateType::class, array(
                "label" => "Date de fin d'inscription",
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => ['class' => 'js-datepicker'],
                'label_attr'=>['class'=>'']
            ))
            ->add('heureCloture', TimeType::class, array(
                "label" => "Heure de fin d'inscription",
                "mapped" => false,
                "minutes" => range(00, 50, 10),
            ))
            ->add('inscriptionsMax', IntegerType::class, array(
                "label" => "Nombre maximum d'inscrits",
                'label_attr'=>['class'=>''],
            ))
            ->add('description', TextareaType::class, array(
                "label" => "Description",
                "required" => false,
                'label_attr'=>['class'=>''],
            ))
            ->add('lieu', EntityType::class, array(
                "label" => "Lieu de l'événement",
                'class' => 'App\Entity\Location',
                'choice_label' => 'name',
                'label_attr'=>['class'=>''],
                'placeholder' => 'Sélectionnez un lieu'
            ))->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $eventToEdit = $event->getData();
                $eventHeureDebut = $event->getForm()->get("heureDebut")->getViewData();
                $eventHeureCloture = $event->getForm()->get("heureCloture")->getViewData();

                $eventToEdit->getDateDebut()->modify("+" . $eventHeureDebut["hour"] . " hours +"
                    . $eventHeureDebut["minute"] . " minutes");
                $eventToEdit->getDateCloture()->modify("+" . $eventHeureCloture["hour"] . " hour +"
                    . $eventHeureCloture["minute"] . " minutes");
            })

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
