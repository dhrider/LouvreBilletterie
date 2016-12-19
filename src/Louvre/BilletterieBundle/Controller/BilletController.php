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
    // affichage de la page d'accueil
    public function indexAction()
    {
        return $this->render('LouvreBilletterieBundle:Billet:index.html.twig');
    }

    ////////////////////////////////////////////////////////////////////////////

    // affichage de la page d'achat de billets
    public function achatAction(Request $request)
    {
        // On récupère une réservation (nouvelle ou en cours selon le retour de la fonction getReservation())
        $reservation = $this->getReservation($request);


        // On crée le formulaire
        $form = $this->get('form.factory')->create(ReservationType::class, $reservation);
        $form->handleRequest($request);

        // Si le requète est "POST" et que le formulaire est valide
        if ($request->isMethod('POST') && $form->isValid()) {
            // On effectue le traitement en base de données
            $em = $this->getDoctrine()->getManager();

            // On crée un nouvel évènement Réservation
            $reservationEvent = new ReservationEvent($reservation);

            // En fonction de l'existence d'un id de réservation
            if (null === $reservation->getId()){ // si oui on dispatch vers une création
                $this->get('event_dispatcher')->dispatch(ReservationEvent::RESERVATION_CREATE, $reservationEvent);
            }
            else { // ou un update
                $this->get('event_dispatcher')->dispatch(ReservationEvent::RESERVATION_UPDATE, $reservationEvent);
            }

            // on persiste dans la BDD
            $em->persist($reservation);
            $em->flush();

            // on redirige en créant une adresse avec l'id at un hash
            return $this->redirect($this->generateUrl('louvre_billetterie_achat_paiement',
                    ['id' => $reservation->getId()]).'#paiement');
        }

        // on affiche le twig achat avec son formulaire
        return $this->render('LouvreBilletterieBundle:Billet:achat.html.twig', array(
            'form' => $form->createView(),
            'reservation' => $reservation
        ));
    }

    ////////////////////////////////////////////////////////////////////////////

    // fonction récupérant l'id de Réservation
    private function getReservation(Request $request) {
        // Si présence d'un id
        if ($request->attributes->has('id')){
            // on récupère la réservation avec cet id dans la BDD
            $reservation = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('LouvreBilletterieBundle:Reservation')
                ->find($request->get('id'))
            ;

            // si rien dans la BDD
            if (null !== $reservation) {
                // on renvoi la variable vide
                return $reservation;
            }
        }

        // On crée une nouvelle réservation
        $reservation = new Reservation();
        // On ajoute un 1er billet vide
        $reservation->addBillet(new Billet());

        // on renvoi la réservation crée
        return $reservation;
    }
}