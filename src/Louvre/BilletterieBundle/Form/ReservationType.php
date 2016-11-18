<?php

namespace Louvre\BilletterieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateReservation',     DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => false,
                'attr' => array(
                    'class' => 'date dateVisite',
                    'style' => 'visibility:hidden'
                )
            ))
            ->add('billets', CollectionType::class,[
                'entry_type' => BilletType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'constraints' => array(new Valid()),
            ])
            ->add('email', RepeatedType::class, array(
                'type' => EmailType::class,
                'invalid_message' => 'Les Emails ne correspondent pas !',
                'required' => true,
                'options' => array('attr' => array('class' => 'email')),
                'first_options' => array('label' => 'Entrez votre email'),
                'second_options' => array('label' => 'Répétez l\'email')
            ))
            ->add('submit', SubmitType::class, array(
                'attr' => array(
                    'class' => 'btn-primary pull-right'
                ),
                'label' => 'RESERVEZ'
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
