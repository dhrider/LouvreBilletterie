<?php

namespace Louvre\BilletterieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
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
                'label_attr' => array(
                  'id' => 'labelReduit'
                ),
                'required' => false,
                'attr' => array(
                    'class' => 'choixReduit'
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
