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
            array('normal',16),
            array('enfant',8),
            array('senior',12),
            array('reduit',10),
            array('gratuit', 0)
        );
        
        foreach ($tarifs as $tarif) {
            $newTarif = new Tarif();
            
            foreach ($tarif as $cle => $valeur) {
                if ($cle === 0) {
                    $newTarif->setNom($valeur);
                }
                else {
                    $newTarif->setTarif($valeur);
                }  
            }

            $manager->persist($newTarif);
        }

        $manager->flush();
    }
}