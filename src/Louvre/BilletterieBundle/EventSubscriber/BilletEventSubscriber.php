<?php

namespace Louvre\BilletterieBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Louvre\BilletterieBundle\Entity\Billet;

class BilletEventSubscriber implements EventSubscriber {

    public  function getSubscribedEvents()
    {
        return array('prePersist', 'preUpdate');
    }

    public function prePersist(LifecycleEventArgs $args) {

        $this->calculTarif($args);
    }

    public function preUpdate(LifecycleEventArgs $args) {
        $this->calculTarif($args);
    }

    private function calculTarif(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Billet) {
            $billet = $entity;

            $repository = $args->getObjectManager()->getRepository('LouvreBilletterieBundle:Tarif');

            $dateReservation =$billet->getReservation()->getDateReservation();
            $dateNaissance = $billet->getDateNaissance();
            $reduit = $billet->getReduit();

            $diffDate = date_diff($dateNaissance, $dateReservation);
            $age = $diffDate->y;

            if ($reduit == false) { // si "tarif réduit" n'est pas coché
                if ($age >= 12 && $age < 60) {
                    $tarif = "normal";
                } elseif ($age >= 4 && $age < 12) {
                    $tarif = "enfant";
                } elseif ($age >= 60) {
                    $tarif = "senior";
                } else {
                    $tarif = "gratuit";
                }
            } else { // si "tarif réduit" est coché
                $tarif = "reduit";
            }
            $data = $repository->selectionTarif($tarif);

            $billet->setMontant($data["tarif"]);
        }
    }
}