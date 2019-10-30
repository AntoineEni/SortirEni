<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form use to manage Location
 * Class LocationType
 * @package App\Form
 */
class LocationType extends AbstractType
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
                "label" => $this->translator->trans("form.location.name"),
            ))
            ->add('street', TextType::class, array(
                "label" => $this->translator->trans("form.location.street"),
            ))
            ->add('latitude', TextType::class, array(
                "label" => $this->translator->trans("form.location.latitude"),
            ))
            ->add('longitude', TextType::class, array(
                "label" => $this->translator->trans("form.location.longitude"),
            ))
            ->add('city', EntityType::class, array(
                'class' => 'App\Entity\City',
                "label" => $this->translator->trans("form.location.city"),
                'choice_label' => 'name',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Location::class,
        ));
    }
}
