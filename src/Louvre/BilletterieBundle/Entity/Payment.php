<?php
//src/Louvre/BilletterieBundle/Entity/Payment.php

namespace Louvre\BilletterieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Louvre\BilletterieBundle\Entity\Reservation;
use Payum\Core\Model\Payment as BasePayment;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class Payment extends BasePayment
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer $id
     */
    protected $id;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @ORM\ManyToOne(targetEntity="Reservation", inversedBy="payments")
     */
    protected $reservation;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @return mixed
     */
    public function getReservation()
    {
        return $this->reservation;
    }


    /**
     * @param mixed $reservation
     * @return Payment
     */
    public function setReservation(Reservation $reservation)
    {
        $this->reservation = $reservation;
        return $this;
    }

}