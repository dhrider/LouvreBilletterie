<?php
// src/Louvre/BilletterieBundle/DataFixtures/ORM/LoadTypeBillet.php

namespace Louvre\BilletterieBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Louvre\BilletterieBundle\Entity\TypeBillet;

class LoadTypeBillet implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $types = array(
            'Journée',
            'Demi-journée'
        );

        foreach ($types as $type) {
            $typeBillet = new TypeBillet();
            
            $typeBillet->setType($type);

            $manager->persist($typeBillet);
        }
        
        $manager->flush();
    }
}