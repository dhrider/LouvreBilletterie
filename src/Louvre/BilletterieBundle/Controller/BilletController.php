<?php
// src/Louvre/BilletterieBundle/BilletController.php

namespace Louvre\BilletterieBundle\Controller;

use Louvre\BilletterieBundle\Entity\Billet;
use Louvre\BilletterieBundle\Entity\Reservation;
use Louvre\BilletterieBundle\Event\ReservationEvent;
use Louvre\BilletterieBundle\Form\ReservationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BilletController extends Controller
{
    public function indexAction()
    {
        return $this->render('LouvreBilletterieBundle:Billet:index.html.twig');
    }
    
    public function achatAction(Request $request)
    {
        $reservation = $this->getReservation($request);

        $form = $this->get('form.factory')->create(ReservationType::class, $reservation);

        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {
            // On effectue le traitement en base de données
            $em = $this->getDoctrine()->getManager();

            $reservationEvent = new ReservationEvent($reservation);

            if (null === $reservation->getId()){
                $this->get('event_dispatcher')->dispatch(ReservationEvent::RESERVATION_CREATE, $reservationEvent);
            }
            else {
                $this->get('event_dispatcher')->dispatch(ReservationEvent::RESERVATION_UPDATE, $reservationEvent);
            }

            $em->persist($reservation);
            $em->flush();

            return $this->redirect($this->generateUrl('louvre_billetterie_achat_paiement',
                    ['id' => $reservation->getId()]).'#paiement');
        }

        return $this->render('LouvreBilletterieBundle:Billet:achat.html.twig', array(
            'form' => $form->createView(),
            'reservation' => $reservation
        ));
    }
    
    public function aproposAction()
    {
        return $this->render('LouvreBilletterieBundle:Billet:apropos.html.twig');
    }

    private function getReservation(Request $request) {
        if ($request->attributes->has('id')){
            $reservation = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('LouvreBilletterieBundle:Reservation')
                ->find($request->get('id'))
            ;

            if (null !== $reservation) {
                return $reservation;
            }
        }

        $reservation = new Reservation();
        $reservation->addBillet(new Billet());

        return $reservation;
    }
}