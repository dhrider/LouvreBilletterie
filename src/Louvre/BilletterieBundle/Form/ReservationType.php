<?php

namespace Louvre\BilletterieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateVisite',     DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'Date de réservation',
                'attr' => array(
                    'class' => 'date dateVisite'
                )
            ))
            ->add('billets', CollectionType::class,[
                'entry_type' => BilletType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('submit', SubmitType::class, array(
                'attr' => array(
                    'class' => 'btn-primary pull-right'
                ),
                'label' => 'Validez le ou les billets'
            ))
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'louvre_billetterie_bundle_reservation';
    }
}
