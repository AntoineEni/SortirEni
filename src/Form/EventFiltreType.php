<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', EntityType::class, array(
                'class' => 'App\Entity\Site',
                'label' => 'Site',
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un lieu',
                'attr'=>['id'=>'site','class'=>'form-control'],
                'label_attr'=>['class'=>'input-group-text']
            ))
            ->add('dateMin',TextType::class,array(
                'attr'=>['id'=>'min','class'=>'form-control js-datepicker'],
                'mapped'=>false,
                'label' => 'To',
                'label_attr'=>['class'=>'input-group-text']
            ))
            ->add('dateMax',TextType::class,array(
                'attr'=>['id'=>'max','class'=>'form-control js-datepicker'],
                'mapped'=>false,
                'label' => 'Date',
                'label_attr'=>['class'=>'input-group-text']
            ))
            ->add('organisateur',CheckboxType::class,array(
                'mapped'=>false,
                'label' => 'Sortie dont je suis l\'organisateur/trice ',
            ))
            ->add('inscrit',CheckboxType::class,array(
                'mapped'=>false,
                'label' => 'Sortie auxqelles je suis inscrit/e ',
            ))
            ->add('nInscrit',CheckboxType::class,array(
                'mapped'=>false,
                'label' => 'Sortie auxqelles je ne suis pas inscrit/e ',
            ))
            ->add('finie',CheckboxType::class,array(
                'mapped'=>false,
                'label' => 'Sortie passées ',
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
