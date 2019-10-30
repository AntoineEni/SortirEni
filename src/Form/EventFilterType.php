<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form use to filter events on the home page
 * Class EventFilterType
 * @package App\Form
 */
class EventFilterType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', EntityType::class, array(
                'class' => 'App\Entity\Site',
                'label' => 'Site',
                'choice_label' => 'name',
                'placeholder' => $this->translator->trans("form.eventfilter.site"),
                'attr' => array(
                    'id' => 'site',
                    'class' => 'form-control',
                ),
                'label_attr' => array(
                    'class' => 'input-group-text',
                )
            ))
            ->add('dateMin',TextType::class, array(
                'mapped' => false,
                'label' => ' ',
                'attr' => array(
                    'id' => 'min',
                    'class' => 'form-control js-datepicker'
                ),
                'label_attr' => array(
                    'class' => 'input-group-text fas fa-calendar-alt',
                )
            ))
            ->add('dateMax',TextType::class, array(
                'mapped' => false,
                'label' => 'Date',
                'attr' => array(
                    'id' => 'max',
                    'class' => 'form-control js-datepicker'),
                'label_attr' => array('
                    class' => 'input-group-text',
                )
            ))
            ->add('organisateur',CheckboxType::class, array(
                'mapped' => false,
                'required' => false,
                'label' => $this->translator->trans("form.eventfilter.organisator"),
            ))
            ->add('inscrit',CheckboxType::class, array(
                'mapped' => false,
                'required' => false,
                'label' => $this->translator->trans("form.eventfilter.inscrit"),
            ))
            ->add('nInscrit',CheckboxType::class, array(
                'mapped' => false,
                'required' => false,
                'label' => $this->translator->trans("form.eventfilter.nInscrit"),
            ))
            ->add('finie',CheckboxType::class, array(
                'mapped' => false,
                'required' => false,
                'label' => $this->translator->trans("form.eventfilter.fini"),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Event::class,
        ));
    }
}
