<?php

namespace Louvre\BilletterieBundle\Form;

use Louvre\BilletterieBundle\Repository\TarifRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BilletType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateVisite',     DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => false,
                'attr' => array(
                    'class' => 'date dateVisite',
                    'style' => 'display:none'
                )
            ))
            ->add('nom',            TextType::class, array(
                'attr' => array(
                    'class' => 'nom',
                    'required',
                    'placeholder' => 'Entrez votre Nom'
                )
            ))
            ->add('prenom',         TextType::class, array(
                'attr' => array(
                    'class' => 'prenom',
                    'required',
                    'placeholder' => 'Entrez votre Prénom'
                )
            ))
            ->add('pays',           CountryType::class, array(
                'data' => 'FR'
            ))
            ->add('dateNaissance',  BirthdayType::class, array(
                'widget' => 'single_text',
                'attr' => array(
                    'class' => 'naissance'
                )
            ))
            ->add('type',           ChoiceType::class, array(
                'choices' => array(
                    'Journée' => 'journee',
                    'Demi-journée' => 'demiJournee'
                ),
                'attr' => array(
                    'class' => 'choixType'
                )
            ))
            ->add('reduit',         CheckboxType::class, array(
                'label' => 'Tarif Réduit',
                'required' => false,
                'attr' => array(
                    'class' => 'choixReduit'
                )
            ))
            ->add('tarif',          HiddenType::class, array(
                'attr' => array(
                    'class' => 'tarif'
                )
            ))
            ->add('montant',        TextType::class, array(
                'label' => 'Montant en €',
                'attr' => array(
                    'class' => 'montant'
                )
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Louvre\BilletterieBundle\Entity\Billet'
        ));
    }
}
