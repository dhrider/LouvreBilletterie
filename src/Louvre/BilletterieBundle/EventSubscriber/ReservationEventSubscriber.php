<?php

namespace Louvre\BilletterieBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Knp\Bundle\SnappyBundle\Snappy\LoggableGenerator;
use Louvre\BilletterieBundle\Entity\Tarif;
use Louvre\BilletterieBundle\Event\ReservationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Routing\Router;

class ReservationEventSubscriber implements EventSubscriberInterface
{
    private $registry;
    private $twigEngine;
    private $mailer;
    private $knpSnappyPdf;
    private $router;

    ///////////////////////////////////////////////////////////////////////////////

    public function __construct(Registry $registry, TwigEngine $twigEngine,LoggableGenerator $knpSnappyBundle, \Swift_Mailer $mailer, Router $router)
    {
        $this->registry = $registry;
        $this->twigEngine = $twigEngine;
        $this->mailer = $mailer;
        $this->knpSnappyPdf = $knpSnappyBundle;
        $this->router = $router;
    }

    ///////////////////////////////////////////////////////////////////////////////

    public static function getSubscribedEvents()
    {
        return array(
            ReservationEvent::RESERVATION_CREATE => array(
                ['calculTarifBillets', 20], ['setTotal', 10] // on définit les priorités d'appel des fonctions
            ),
            ReservationEvent::RESERVATION_UPDATE => array(
                ['calculTarifBillets', 20], ['setTotal', 10]
            ),
            ReservationEvent::RESERVATION_PAYMENT_SUCCESS => array(
                ['createPdf', 20], ['sendEmail', 10]
            ),
            ReservationEvent::RESERVATION_PAYMENT_FAILED => array(
                'erreurPaiement'
            )
        );
    }

    ///////////////////////////////////////////////////////////////////////////////

    public function getListeTarif()
    {
        // on récupère tous les tarifs existants
        $tarifsAll = $this->registry
            ->getRepository('LouvreBilletterieBundle:Tarif')
            ->findAll();

        // On crée un tableau vide qui recevra la liste des tarifs
        $tarifs = array();
        // on boucle sur les tarifs récupérés et on remplit le tableau
        foreach ($tarifsAll as $tarif)
        {
            $tarifs[$tarif->getNom()] = $tarif;
        }

        return $tarifs;
    }

    ///////////////////////////////////////////////////////////////////////////////

    public function getNomTarif($age, $reduit)
    {
        if ($reduit == false) // si "tarif réduit" n'est pas coché
        {
            // on affecte le nom du tarif en fonction de l'âge
            if ($age >= 12 && $age < 60)
            {
                $tarif = "normal";
            }
            elseif ($age >= 4 && $age < 12)
            {
                $tarif = "enfant";
            }
            elseif ($age >= 60)
            {
                $tarif = "senior";
            }
            else
            {
                $tarif = "gratuit";
            }
        }
        else // si "tarif réduit" est coché
        {
            $tarif = "reduit";
        }

        return $tarif;
    }

    ///////////////////////////////////////////////////////////////////////////////

    // fonction de calcul du tarif du billet en fonction des données du visiteur
    public function calculTarifBillets(ReservationEvent $reservationEvent)
    {
        // on récupère la liste des tarifs
        $tarifs = $this->getListeTarif();

        // on récupère la date de réservation
        $dateReservation = $reservationEvent->getReservation()->getDateReservation();

        // Pour chaque billet de la réservation
        foreach ($reservationEvent->getReservation()->getBillets() as &$billet)
        {
            // on récupère la date de naissance
            $dateNaissance = $billet->getDateNaissance();

            // on calcule l'âge du visiteur
            $age = date_diff($dateNaissance, $dateReservation)->y;
            // si on a sélectionné le tarif réduit ou non
            $reduit = $billet->getReduit();

            // on stocke le tarif correspondant
            $tarif = $this->getNomTarif($age,$reduit);

            // on affecte l'id du tarif au billet
            $billet->setTarif($tarifs[$tarif]);
            // on affecte le montant du montant
            $billet->setMontant($tarifs[$tarif]->getTarif());
        }
    }

    ///////////////////////////////////////////////////////////////////////////////

    // fonction de calcul du montant total de la réservation
    public function setTotal(ReservationEvent $reservationEvent)
    {
        // on récupère la réservation
        $reservation = $reservationEvent->getReservation();

        $montantTotal = 0;

        // on boucle sur chaque billet de la réservation
        foreach ($reservation->getBillets() as $billet)
        {
            // on incrémente le montant total en récupérant le montant du billet
            $montantTotal += $billet->getMontant();
        }

        // on affecte le montant total
        $reservation->setTotal($montantTotal);
    }

    ///////////////////////////////////////////////////////////////////////////////

    // fonction de création du pdf contenant les billets
    public function createPdf(ReservationEvent $reservationEvent)
    {
        $reservation = $reservationEvent->getReservation();

        $pdfPath = __DIR__.'/../../../../web/upload/reservation_'.$reservation->getId().'.pdf';

        $pdfHtml = $this->twigEngine->render('@LouvreBilletterie/pdfBillet.html.twig',array(
            'reservation' => $reservation,
            'billets' => $reservation->getBillets()
        ));

        $this->knpSnappyPdf->generateFromHtml($pdfHtml,$pdfPath);
    }

    ///////////////////////////////////////////////////////////////////////////////

    // fonction d'envoi des billets par email
    public function sendEmail(ReservationEvent $reservationEvent)
    {
        $reservation = $reservationEvent->getReservation();

        $imagePath = __DIR__.'/../../../../web/bundles/louvrebilletterie/image/';
        $pdfPath = __DIR__.'/../../../../web/upload/reservation_'.$reservation->getId().'.pdf';

        /* @var \Swift_Message $email */
        $email =  \Swift_Message::newInstance()
            ->setSubject('Confirmation de votre réservation')
            ->setFrom('billetterie@louvrebilletterie.com')
            ->setTo($reservation->getEmail())
            ->setContentType('text/html')
        ;

        $image = $email->embed(\Swift_Image::fromPath($imagePath.'louvre_logo_frise.png'));

        $email->attach(\Swift_Attachment::fromPath($pdfPath))
            ->setBody($this->twigEngine->render('@LouvreBilletterie/emailBillet.html.twig',
                array('reservation' => $reservation,'logo' => $image))
            );

        $this->mailer->send($email);
    }

    ///////////////////////////////////////////////////////////////////////////////

    // fonction renvoyant à l'onglet de validation du paiement si ce dernier c'est mal passé
    public function erreurPaiement( ReservationEvent $reservationEvent)
    {
        return $this->router->generate('louvre_billetterie_achat_paiement',
                ['id' => $reservationEvent->getReservation()->getId()]).'#paiement';
    }
}