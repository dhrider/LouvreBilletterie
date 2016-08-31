<?php
// src/Louvre/BilletterieBundle/BilletController.php

namespace Louvre\BilletterieBundle\Controller;

use Louvre\BilletterieBundle\Entity\Billet;
use Louvre\BilletterieBundle\Entity\Reservation;
use Louvre\BilletterieBundle\Form\ReservationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BilletController extends Controller
{
    public function indexAction()
    {
        return $this->render('LouvreBilletterieBundle:Billet:index.html.twig');
    }
    
    public function achatAction()
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

    public function addAction()
    {
       
    }
}