<?php

namespace Louvre\BilletterieBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Louvre\BilletterieBundle\Event\ReservationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReservationEventSubscriber implements EventSubscriberInterface {

    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public static function getSubscribedEvents()
    {
        return array(
            ReservationEvent::RESERVATION_CREATE => array(
                ['calculTarifBillets', 20], ['setTotal', 10]
            ),
            ReservationEvent::RESERVATION_UPDATE => array('calculTarifBillets', 'setTotal')
        );
    }

    public function calculTarifBillets(ReservationEvent $reservationEvent)
    {

        $tarifs = array();

        $tarifsAll = $this->registry->getEntityManager()->getRepository('LouvreBilletterieBundle:Tarif')->findAll();
        foreach ($tarifsAll as $tarif) {
            $tarifs[$tarif->getNom()] = $tarif;
        }


        foreach ($reservationEvent->getReservation()->getBillets() as &$billet) {
            $dateReservation = $billet->getReservation()->getDateReservation();
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

            $billet->setTarif($tarifs[$tarif]);

            $billet->setMontant($tarifs[$tarif]->getTarif());

        }

    }

    public function setTotal(ReservationEvent $reservationEvent)
    {

        $entity = $reservationEvent->getReservation();

        $reservation = $entity;

        $montantTotal = 0;

        foreach ($reservation->getBillets() as $billet) {
            $montantTotal += $billet->getMontant();
        }

        $reservation->setTotal($montantTotal);
        dump($entity);
    }
}