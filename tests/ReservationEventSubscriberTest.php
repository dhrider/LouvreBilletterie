<?php

use Louvre\BilletterieBundle\EventSubscriber\ReservationEventSubscriber;
use Louvre\BilletterieBundle\Event\ReservationEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Knp\Bundle\SnappyBundle\Snappy\LoggableGenerator;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Routing\Router;
use Louvre\BilletterieBundle\Repository\TarifRepository;
use Louvre\BilletterieBundle\Entity\Reservation;
use Louvre\BilletterieBundle\Entity\Billet;
use Louvre\BilletterieBundle\Entity\Tarif;


class ReservationEventSubscriberTest extends PHPUnit_Framework_TestCase
{
    protected $reservationEventSubscriber;
    protected $tarifRepository;

    public function setUp()
    {
        $twigEngine = $this->getMockBuilder(TwigEngine::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mailer = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $knpSnappyPdf = $this->getMockBuilder(LoggableGenerator::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $router = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $registry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->tarifRepository = $this->getMockBuilder(TarifRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $registry->method('getRepository')->willReturn($this->tarifRepository);

        $this->reservationEventSubscriber = new ReservationEventSubscriber(
                $registry,
                $twigEngine,
                $knpSnappyPdf,
                $mailer,
                $router
            )
        ;
    }

    public function simulationReservation($dateReservation, $dateNaissance, $reduit)
    {
        $reservation = new Reservation();
        $reservation->setDateReservation(new \DateTime($dateReservation));

        $billet = new Billet();
        $billet->setDateNaissance(new \DateTime($dateNaissance));
        $billet->setReduit($reduit);

        $reservation->addBillet($billet);

        return $reservation;
    }

    public function simulationTarif($nom,$montant)
    {
        $t = new Tarif();
        $t->setNom($nom);
        $t->setTarif($montant);
        $tarif = array($nom => $t);

        return $tarif;
    }

    public function testCalculTarifBilletsNormal()
    {
        $reservation = $this->simulationReservation('2016-01-01','2000-01-01',false);

        $tarif = $this->simulationTarif('normal',16);

        $this->tarifRepository->method("findAll")->willReturn($tarif);

        $this->reservationEventSubscriber->calculTarifBillets(new ReservationEvent($reservation));

        $this->assertEquals(16, $reservation->getBillets()[0]->getMontant());
    }

    public function testCalculTarifBilletsNormalReduit()
    {
        $reservation = $this->simulationReservation('2016-01-01','2000-01-01',true);

        $tarif = $this->simulationTarif('reduit',8);

        $this->tarifRepository->method("findAll")->willReturn($tarif);

        $this->reservationEventSubscriber->calculTarifBillets(new ReservationEvent($reservation));

        $this->assertEquals(8, $reservation->getBillets()[0]->getMontant());
    }

    public function testCalculTarifBilletsEnfant()
    {
        $reservation = $this->simulationReservation('2016-01-01','2010-01-01',false);

        $tarif = $this->simulationTarif('enfant',8);

        $this->tarifRepository->method("findAll")->willReturn($tarif);

        $this->reservationEventSubscriber->calculTarifBillets(new ReservationEvent($reservation));

        $this->assertEquals(8, $reservation->getBillets()[0]->getMontant());
    }

    public function testCalculTarifBilletsEnfantReduit()
    {
        $reservation = $this->simulationReservation('2016-01-01','2010-01-01',true);

        $tarif = $this->simulationTarif('reduit',4);

        $this->tarifRepository->method("findAll")->willReturn($tarif);

        $this->reservationEventSubscriber->calculTarifBillets(new ReservationEvent($reservation));

        $this->assertEquals(4, $reservation->getBillets()[0]->getMontant());
    }

    public function testCalculTarifBilletsSenior()
    {
        $reservation = $this->simulationReservation('2016-01-01','1950-01-01',false);

        $tarif = $this->simulationTarif('senior',12);

        $this->tarifRepository->method("findAll")->willReturn($tarif);

        $this->reservationEventSubscriber->calculTarifBillets(new ReservationEvent($reservation));

        $this->assertEquals(12, $reservation->getBillets()[0]->getMontant());
    }

    public function testCalculTarifBilletsSeniorReduit()
    {
        $reservation = $this->simulationReservation('2016-01-01','1950-01-01',true);

        $tarif = $this->simulationTarif('reduit',6);

        $this->tarifRepository->method("findAll")->willReturn($tarif);

        $this->reservationEventSubscriber->calculTarifBillets(new ReservationEvent($reservation));

        $this->assertEquals(6, $reservation->getBillets()[0]->getMontant());
    }

    public function testCalculTarifBilletsGratuit()
    {
        $reservation = $this->simulationReservation('2016-01-01','2015-01-01',false);

        $tarif = $this->simulationTarif('gratuit',0);

        $this->tarifRepository->method("findAll")->willReturn($tarif);

        $this->reservationEventSubscriber->calculTarifBillets(new ReservationEvent($reservation));

        $this->assertEquals(0, $reservation->getBillets()[0]->getMontant());
    }


    public function testGetNomTarifNormal()
    {
        $this->assertEquals("normal",$this->reservationEventSubscriber->getNomTarif(15,false));
    }

    public function testGetNomTarifEnfant()
    {
        $this->assertEquals("enfant",$this->reservationEventSubscriber->getNomTarif(11,false));
    }

    public function testGetNomTarifSenior()
    {
        $this->assertEquals("senior",$this->reservationEventSubscriber->getNomTarif(61,false));
    }

    public function testGetNomTarifGratuit()
    {
        $this->assertEquals("gratuit",$this->reservationEventSubscriber->getNomTarif(3,false));
    }

    public function testGetNomTarifReduit()
    {
        $this->assertEquals("reduit",$this->reservationEventSubscriber->getNomTarif(15,true));
    }


}