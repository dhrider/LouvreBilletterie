<?php

namespace Louvre\BilletterieBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Louvre\BilletterieBundle\Entity\Reservation;

class ReservationEventSubscriber implements EventSubscriber {

    public  function getSubscribedEvents()
    {
        return array('prePersist', 'preUpdate');
    }

    public function prePersist(LifecycleEventArgs $args) {
        dump('persist');
        if ($args->getObject() instanceof Reservation) {
            $this->setTotal($args->getObject());
        }
    }



    public function setTotal(Reservation $reservation)
    {
        foreach ($reservation->getBillets() as &$billet) {

            $montant = $billet->getTarif();

            if($montant != 0 && $billet->getType() =='demiJournee') {
                $montant = $montant/2;
            }

            $billet->setMontant($montant);
        }
        $reservation->setTotal($reservation->getMontantTotal());
    }
}