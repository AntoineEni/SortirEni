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

class EventFiltreType extends AbstractType
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
                'attr'=>['id'=>'site','class'=>'form-control'],
                'label_attr'=>['class'=>'input-group-text']
            ))
            ->add('dateMin',TextType::class,array(
                'attr'=>['id'=>'min','class'=>'form-control js-datepicker'],
                'mapped'=>false,
                'label' => ' ',
                'label_attr'=>['class'=>'input-group-text fas fa-calendar-alt']
            ))
            ->add('dateMax',TextType::class,array(
                'attr'=>['id'=>'max','class'=>'form-control js-datepicker'],
                'mapped'=>false,
                'label' => 'Date',
                'label_attr'=>['class'=>'input-group-text']
            ))
            ->add('organisateur',CheckboxType::class,array(
                'mapped'=>false,
                'label' => $this->translator->trans("form.eventfilter.organisator"),
            ))
            ->add('inscrit',CheckboxType::class,array(
                'mapped'=>false,
                'label' => $this->translator->trans("form.eventfilter.inscrit"),
            ))
            ->add('nInscrit',CheckboxType::class,array(
                'mapped'=>false,
                'label' => $this->translator->trans("form.eventfilter.nInscrit"),
            ))
            ->add('finie',CheckboxType::class,array(
                'mapped'=>false,
                'label' => $this->translator->trans("form.eventfilter.fini"),
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
