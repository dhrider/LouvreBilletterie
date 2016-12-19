<?php

namespace Louvre\BilletterieBundle\Event;

use Louvre\BilletterieBundle\Entity\Reservation;
use Symfony\Component\EventDispatcher\Event;

class ReservationEvent extends Event
{
    const RESERVATION_CREATE = 'reservation.create';
    const RESERVATION_UPDATE = 'reservation.update';
    const RESERVATION_PAYMENT_SUCCESS = 'reservation.payment.success';
    const RESERVATION_PAYMENT_FAILED = 'reservation.payment.failed';

    private $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function getReservation()
    {
        return $this->reservation;
    }
}