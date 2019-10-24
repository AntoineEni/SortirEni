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
                'label' => 'Site :',
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un lieu',
                'attr'=>['id'=>'site','class'=>'form-control'],
                'label_attr'=>['class'=>'col-form-label']
            ))
            ->add('dateMin',TextType::class,array(
                'attr'=>['id'=>'min','class'=>'form-control js-datepicker'],
                'mapped'=>false,
                'label_attr'=>['class'=>'col-form-label']
            ))
            ->add('dateMax',TextType::class,array(
                'attr'=>['id'=>'max','class'=>'form-control js-datepicker'],
                'mapped'=>false,
                'label_attr'=>['class'=>'col-form-label']
            ))
            ->add('organisateur',CheckboxType::class,array(
                'attr'=>['id'=>'organisateur','class'=>'form-control'],
                'mapped'=>false,
                'label_attr'=>['class'=>'col-form-label']
            ))
            ->add('inscrit',CheckboxType::class,array(
                'attr'=>['id'=>'inscrit','class'=>'form-control'],
                'mapped'=>false,
                'label_attr'=>['class'=>'col-form-label']
            ))
            ->add('nInscrit',CheckboxType::class,array(
                'attr'=>['id'=>'Ninscrit','class'=>'form-control'],
                'mapped'=>false,
                'label_attr'=>['class'=>'col-form-label']
            ))
            ->add('finie',CheckboxType::class,array(
                'attr'=>['id'=>'passer','class'=>'form-control'],
                'mapped'=>false,
                'label_attr'=>['class'=>'col-form-label']
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
