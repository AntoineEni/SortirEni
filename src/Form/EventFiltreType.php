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
                'attr'=>['id'=>'organisateur','class'=>''],
                'mapped'=>false,
                'label' => 'Sortie dont je suis l\'organisateur/trice :',
                'label_attr'=>['class'=>'form-control']
            ))
            ->add('inscrit',CheckboxType::class,array(
                'attr'=>['id'=>'inscrit','class'=>''],
                'mapped'=>false,
                'label' => 'Sortie auxqelles je suis inscrit/e :',
                'label_attr'=>['class'=>'form-control']
            ))
            ->add('nInscrit',CheckboxType::class,array(
                'attr'=>['id'=>'Ninscrit','class'=>''],
                'mapped'=>false,
                'label' => 'Sortie auxqelles je ne suis pas inscrit/e :',
                'label_attr'=>['class'=>'form-control']
            ))
            ->add('finie',CheckboxType::class,array(
                'attr'=>['id'=>'passer','class'=>' '],
                'mapped'=>false,
                'label' => 'Sortie passées :',
                'label_attr'=>['class'=>'form-control']
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
