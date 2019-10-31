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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form use to manage Event
 * Class EventType
 * @package App\Form
 */
class EventType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                "label" => $this->translator->trans("form.event.name"),
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('dateDebut', DateType::class, array(
                "label" => $this->translator->trans("form.event.datedebut"),
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'class' => 'js-datepicker form-control',
                )
            ))
            ->add('heureDebut', TimeType::class, array(
                "label" => $this->translator->trans("form.event.heuredebut"),
                "mapped" => false,
                "minutes" => range(00, 50, 10),
            ))
            ->add('duration', IntegerType::class, array(
                "label" => $this->translator->trans("form.event.duration"),
            ))
            ->add('dateCloture', DateType::class, array(
                "label" => $this->translator->trans("form.event.datecloture"),
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'class' => 'js-datepicker',
                ),
            ))
            ->add('heureCloture', TimeType::class, array(
                "mapped" => false,
                "minutes" => range(00, 50, 10),
            ))
            ->add('inscriptionsMax', IntegerType::class, array(
                "label" => $this->translator->trans("form.event.inscriptionsmax"),
            ))
            ->add('description', TextareaType::class, array(
                "label" => $this->translator->trans("form.event.description"),
                "required" => false,
            ))
            ->add('lieu', EntityType::class, array(
                "label" => $this->translator->trans("form.event.lieu.label"),
                'class' => 'App\Entity\Location',
                'choice_label' => 'name',
                'placeholder' => $this->translator->trans("form.event.lieu.placeholder"),
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
