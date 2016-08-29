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
            array('normal','journee',16),
            array('normal','demi',8),
            array('enfant','journee',8),
            array('enfant','demi',4),
            array('senior','journee',12),
            array('senior','demi',6),
            array('reduit','journee',10),
            array('reduit','demi',5),
            array('famille','journee',35),
            array('famille','demi',17.5)
        );
        
        foreach ($tarifs as $tarif) {
            $newTarif = new Tarif();
            
            foreach ($tarif as $cle => $valeur) {
                if ($cle === 0) {
                    $newTarif->setNom($valeur);
                }
                elseif ($cle === 1) {
                    $newTarif->setType($valeur);
                }
                else {
                    $newTarif->setMontant($valeur);
                }  
            }

            $manager->persist($newTarif);
        }

        $manager->flush();
    }
}