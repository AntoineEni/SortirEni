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

/**
 * Form use to manage Event
 * Class EventType
 * @package App\Form
 */
class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                "label" => "Titre de l'evenement",
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('dateDebut', DateType::class, array(
                "label" => "Date de début",
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'class' => 'js-datepicker form-control',
                )
            ))
            ->add('heureDebut', TimeType::class, array(
                "label" => "as",
                "mapped" => false,
                "minutes" => range(00, 50, 10),
            ))
            ->add('duration', IntegerType::class, array(
                "label" => "Durée (en minutes)",
            ))
            ->add('dateCloture', DateType::class, array(
                "label" => "Date de fin d'inscription",
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'class' => 'js-datepicker',
                ),
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
            ))->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                //Add time to event dates when submit the form
                $eventToEdit = $event->getData();

                //Get the times values (array_map is use to convert the null yo 0)
                $eventHeureDebut = array_map("intval", $event->getForm()->get("heureDebut")->getViewData());
                $eventHeureCloture = array_map("intval", $event->getForm()->get("heureCloture")->getViewData());

                $eventToEdit->getDateDebut()->modify("+" . $eventHeureDebut["hour"] . " hours +"
                    . $eventHeureDebut["minute"] . " minutes");
                $eventToEdit->getDateCloture()->modify("+" . $eventHeureCloture["hour"] . " hour +"
                    . $eventHeureCloture["minute"] . " minutes");
            })

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Event::class,
        ));
    }
}
