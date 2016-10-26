<?php

namespace Louvre\BilletterieBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Louvre\BilletterieBundle\Entity\Reservation;

class ReservationEventSubscriber implements EventSubscriber {

    public  function getSubscribedEvents()
    {
        return array('prePersist','preUpdate');
    }

    public function prePersist(LifecycleEventArgs $args) {
        $this->setTotal($args);
    }

    public function preUpdate(LifecycleEventArgs $args) {
        $this->setTotal($args);
    }

    public function setTotal(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Reservation) {
            $reservation = $entity;

            $montantTotal = 0;

            foreach ($reservation->getBillets() as $billet) {
                $montantTotal += $billet->getMontant();
            }

            $reservation->setTotal($montantTotal);
        }
    }
}