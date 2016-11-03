<?php

namespace Louvre\BilletterieBundle\Event;

use Louvre\BilletterieBundle\Entity\Reservation;
use Symfony\Component\EventDispatcher\Event;

class ReservationEvent extends Event {
    const RESERVATION_CREATE = 'reservation.create';
    const RESERVATION_UPDATE = 'reservation.update';

    private $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function getReservation() {
        return $this->reservation;
    }
}