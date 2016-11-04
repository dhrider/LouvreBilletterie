<?php
// src/Louvre/BilletterieBundle/Controller/PaymentController.php

namespace Louvre\BilletterieBundle\Controller;

use Louvre\BilletterieBundle\Entity\Billet;
use Louvre\BilletterieBundle\Entity\Reservation;
use Louvre\BilletterieBundle\Event\ReservationEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Payum\Core\Request\GetHumanStatus;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends Controller
{
    public function prepareAction(Request $request, Reservation $reservation)
    {
        $reservation->setEmail($request->request->get('email_reservation'));
        $this->getDoctrine()->getManager()->persist($reservation);
        $this->getDoctrine()->getManager()->flush();

        $gatewayName = 'stripe';

        $storage = $this->get('payum')->getStorage('Louvre\BilletterieBundle\Entity\Payment');

        $payment = $storage->create();
        $payment->setReservation($reservation);
        $payment->setNumber(uniqid());
        $payment->setCurrencyCode('EUR');
        $payment->setTotalAmount($reservation->getTotal()*100);
        $payment->setDescription('Billet(s) Louvre');
        $payment->setClientId($reservation->getId());
        $payment->setClientEmail($reservation->getEmail());

        $storage->update($payment);

        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $payment,
            'louvre_payment_done', // the route to redirect after capture
            array('id' => $reservation->getId())
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

    public function doneAction(Request $request, Reservation $reservation)
    {
        $token = $this->get('payum')->getHttpRequestVerifier()->verify($request);

        $gateway = $this->get('payum')->getGateway($token->getGatewayName());

        // or Payum can fetch the model for you while executing a request (Preferred).
        $gateway->execute($status = new GetHumanStatus($token));
        $payment = $status->getFirstModel();

        $reservationEvent = new ReservationEvent($reservation);

        if($status->isCaptured())
        {
            $this->get('event_dispatcher')
                ->dispatch(ReservationEvent::RESERVATION_PAYMENT_SUCCESS,$reservationEvent);
        }
        else
        {
            $this->get('event_dispatcher')
                ->dispatch(ReservationEvent::RESERVATION_PAYMENT_FAILED, $reservationEvent);
        }

        return $this->redirect($this->generateUrl('louvre_billetterie_achat_paiement',
            ['id' => $payment->getReservation()->getId()]).'#confirmation');
    }
}