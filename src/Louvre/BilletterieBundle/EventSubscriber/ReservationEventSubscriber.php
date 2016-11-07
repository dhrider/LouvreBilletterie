<?php

namespace Louvre\BilletterieBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Knp\Bundle\SnappyBundle\Snappy\LoggableGenerator;
use Louvre\BilletterieBundle\Event\ReservationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Routing\Router;

class ReservationEventSubscriber implements EventSubscriberInterface {

    private $registry;
    private $twigEngine;
    private $mailer;
    private $knpSnappyPdf;
    private $router;

    public function __construct(Registry $registry, TwigEngine $twigEngine,LoggableGenerator $knpSnappyBundle, \Swift_Mailer $mailer, Router $router)
    {
        $this->registry = $registry;
        $this->twigEngine = $twigEngine;
        $this->mailer = $mailer;
        $this->knpSnappyPdf = $knpSnappyBundle;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return array(
            ReservationEvent::RESERVATION_CREATE => array(
                ['calculTarifBillets', 20], ['setTotal', 10]
            ),
            ReservationEvent::RESERVATION_UPDATE => array(
                ['calculTarifBillets', 20], ['setTotal', 10]
            ),
            ReservationEvent::RESERVATION_PAYMENT_SUCCESS => array(
                'pdfAndMail'
            ),
            ReservationEvent::RESERVATION_PAYMENT_FAILED => array(
                'erreurPaiement'
            )
        );
    }

    public function calculTarifBillets(ReservationEvent $reservationEvent)
    {
        $tarifs = array();

        $tarifsAll = $this->registry
            ->getEntityManager()
            ->getRepository('LouvreBilletterieBundle:Tarif')
            ->findAll();

        foreach ($tarifsAll as $tarif) {
            $tarifs[$tarif->getNom()] = $tarif;
        }

        foreach ($reservationEvent->getReservation()->getBillets() as &$billet) {
            $dateReservation = $billet->getReservation()->getDateReservation();
            $dateNaissance = $billet->getDateNaissance();
            $reduit = $billet->getReduit();

            $diffDate = date_diff($dateNaissance, $dateReservation);
            $age = $diffDate->y;

            if ($reduit == false) { // si "tarif réduit" n'est pas coché
                if ($age >= 12 && $age < 60) {
                    $tarif = "normal";
                } elseif ($age >= 4 && $age < 12) {
                    $tarif = "enfant";
                } elseif ($age >= 60) {
                    $tarif = "senior";
                } else {
                    $tarif = "gratuit";
                }
            } else { // si "tarif réduit" est coché
                $tarif = "reduit";
            }

            $billet->setTarif($tarifs[$tarif]);

            $billet->setMontant($tarifs[$tarif]->getTarif());
        }
    }

    public function setTotal(ReservationEvent $reservationEvent)
    {
        $reservation = $reservationEvent->getReservation();

        $montantTotal = 0;

        foreach ($reservation->getBillets() as $billet) {
            $montantTotal += $billet->getMontant();
        }

        $reservation->setTotal($montantTotal);
    }

    public function pdfAndMail(ReservationEvent $reservationEvent)
    {
        $reservation = $reservationEvent->getReservation();

        $reservation->setStatut($reservation::STATUS_PAYER);

        $this->registry->getEntityManager()->persist($reservation);
        $this->registry->getEntityManager()->flush();

        $pdfPath = __DIR__.'/../../../../web/upload/reservation_'.$reservation->getId().'.pdf';
        $imagePath = __DIR__.'/../../../../web/bundles/louvrebilletterie/image/';

        $pdfHtml = $this->twigEngine->render('@LouvreBilletterie/pdfBillet.html.twig',array(
            'reservation' => $reservation,
            'billets' => $reservation->getBillets()
        ));

        $this->knpSnappyPdf->generateFromHtml($pdfHtml,$pdfPath);

        /* @var \Swift_Message $email */
        $email =  \Swift_Message::newInstance()
            ->setSubject('Test')
            ->setFrom('Louvre@test.com')
            ->setTo('p_bordmann@orange.fr')
            ->setContentType('text/html')
        ;

        $image = $email->embed(\Swift_Image::fromPath($imagePath.'louvre_logo_frise.png'));

        $email->attach(\Swift_Attachment::fromPath($pdfPath))
            ->setBody($this->twigEngine->render('@LouvreBilletterie/emailBillet.html.twig',
                array('reservation' => $reservation,'logo' => $image))
            );

        $this->mailer->send($email);
    }

    public function erreurPaiement( ReservationEvent $reservationEvent) {
        return $this->router->generate('louvre_billetterie_achat_paiement',
                ['id' => $reservationEvent->getReservation()->getId()]).'#paiement';
    }
}