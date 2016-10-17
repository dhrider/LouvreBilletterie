<?php
// src/Louvre/BilletterieBundle/BilletController.php

namespace Louvre\BilletterieBundle\Controller;

use Louvre\BilletterieBundle\Entity\Billet;
use Louvre\BilletterieBundle\Entity\Reservation;
use Louvre\BilletterieBundle\Form\ReservationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Payum\Core\Request\GetHumanStatus;

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
            $em->persist($reservation);
            $em->flush();

            return $this->redirect($this->generateUrl('louvre_billetterie_achat_paiement', ['id' => $reservation->getId()]));
        }

        return $this->render('LouvreBilletterieBundle:Billet:achat.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function aproposAction()
    {
        return $this->render('LouvreBilletterieBundle:Billet:apropos.html.twig');
    }

    public function remplitarifAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            // récupération des variables envoyées par la requète
            $naissance = date_create($request->request->get('naissance'));
            $dateVisite = date_create($request->request->get('dateVisite'));
            $reduit = $request->request->get('reduit');

            // on calcule la différence entre la date de naissance et la date visite
            $diffDate = date_diff($naissance,$dateVisite);
            // pour connaître l'âge du visiteur
            $age = $diffDate->y;

            // on définit le tarif en fonction de l'âge
            if ($reduit === "non") { // si "tarif réduit" n'est pas coché
                if ($age >= 12 && $age < 60) {
                    $tarif = "normal";
                } elseif ($age >= 4 && $age < 12) {
                    $tarif = "enfant";
                } elseif ($age >= 60) {
                    $tarif = "senior";
                } else {
                    $tarif = "gratuit";
                }
            }
            else { // si "tarif réduit" est coché
                $tarif = "reduit";
            }

            if ($tarif !== "") { // on vérifie qu'on a bien obtenu un tarif
                // on récupère le répository
                $repository = $this
                            ->getDoctrine()
                            ->getManager()
                            ->getRepository('LouvreBilletterieBundle:Tarif')
                ;
                // on appelle la fonction de sélection du tarif
                $data = $repository->selectionTarif($tarif);

                // on renvoi la réponse sous format JSON
                return new JsonResponse($data);
            }
        }
        return new  Response("Aucun tarif valide trouvé");
    }

    public function recapReservationAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $idReservation = $request->request->get('idReservation');

            if ($idReservation !== "") {
                $repository = $this
                    ->getDoctrine()
                    ->getManager()
                    ->getRepository('LouvreBilletterieBundle:Billet')
                ;

                $data = $repository->recupReservation($idReservation);

                return new JsonResponse($data);
            }
        }

        return new Response('Aucune Réservation trouvée !');
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

    public function prepareAction() {
        $gatewayName = 'offline';

        $storage = $this->get('payum')->getStorage('Louvre\BilletterieBundle\Entity\Payment');

        $payment = $storage->create();
        $payment->setNumber(uniqid());
        $payment->setCurrencyCode('EUR');
        $payment->setTotalAmount(123); // 1.23 EUR
        $payment->setDescription('A description');
        $payment->setClientId('anId');
        $payment->setClientEmail('foo@example.com');

        $storage->update($payment);

        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $payment,
            'done' // the route to redirect after capture
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

    public function doneAction(Request $request)
    {
        $token = $this->get('payum')->getHttpRequestVerifier()->verify($request);

        $gateway = $this->get('payum')->getGateway($token->getGatewayName());

        // you can invalidate the token. The url could not be requested any more.
        // $this->get('payum')->getHttpRequestVerifier()->invalidate($token);

        // Once you have token you can get the model from the storage directly.
        //$identity = $token->getDetails();
        //$payment = $this->get('payum')->getStorage($identity->getClass())->find($identity);

        // or Payum can fetch the model for you while executing a request (Preferred).
        $gateway->execute($status = new GetHumanStatus($token));
        $payment = $status->getFirstModel();

        // you have order and payment status
        // so you can do whatever you want for example you can just print status and payment details.

        return new JsonResponse(array(
            'status' => $status->getValue(),
            'payment' => array(
                'total_amount' => $payment->getTotalAmount(),
                'currency_code' => $payment->getCurrencyCode(),
                'details' => $payment->getDetails(),
            ),
        ));
    }
}