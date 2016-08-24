<?php
// src/Louvre/BilletterieBundle/DataFixtures/ORM/LoadTarif.php

namespace Louvre\BilletterieBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Louvre\BilletterieBundle\Entity\Tarif;

class LoadTarif implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tarifs = array(
            'normal' => 16,
            'enfant' => 8,
            'senior' => 12,
            'réduit' => 10,
            'famille' => 35
        );

        foreach ($tarifs as $cle => $valeur) {
            $newTarif = new Tarif();
            
            $newTarif->setNom($cle);
            $newTarif->setMontant($valeur);
            
            $manager->persist($newTarif);            
        }
        
        $manager->flush();
    }
}