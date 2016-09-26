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

class BilletController extends Controller
{
    public function indexAction()
    {
        return $this->render('LouvreBilletterieBundle:Billet:index.html.twig');
    }
    
    public function achatAction(Request $request)
    {
        $billet = new Billet();
        $reservation = new Reservation();
        $reservation->addBillet($billet);

        $form = $this->get('form.factory')->create(ReservationType::class, $reservation);


        return $this->render('LouvreBilletterieBundle:Billet:achat.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function aproposAction()
    {
        return $this->render('LouvreBilletterieBundle:Billet:apropos.html.twig');
    }

    public function remplitarifAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $naissance = date_create($request->request->get('naissance'));
            $dateVisite = date_create($request->request->get('dateVisite'));
            $reduit = $request->request->get('reduit');

            $diffDate = date_diff($naissance,$dateVisite);
            $age = $diffDate->y;

            if ($reduit === "non") {
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
            else {
                if ($age >= 18 && $age <= 65) {
                    $tarif = "reduit";
                }
                else {
                    return new Response("Le tarif réduit ne s'applique pas pour votre tranche d'âge");
                }
            }

            if ($tarif !== "") {
                $repository = $this
                            ->getDoctrine()
                            ->getManager()
                            ->getRepository('LouvreBilletterieBundle:Tarif')
                ;
                $data = $repository->selectionTarif($tarif);

                return new JsonResponse($data);
            }

            return new  Response("Aucun tarif valide trouvé");
        }
        return new  Response("Aucun tarif valide trouvé");
    }


}