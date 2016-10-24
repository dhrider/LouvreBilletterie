<?php
// src/Louvre/BilletterieBundle/Controller/PaymentController.php

namespace Louvre\BilletterieBundle\Controller;

use Louvre\BilletterieBundle\Entity\Billet;
use Louvre\BilletterieBundle\Entity\Reservation;
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
        $payment->setTotalAmount($reservation->getMontantTotal()*100); // 1.23 EUR
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

    public function doneAction(Request $request, Reservation $reservation, Billet $billets)
    {
        $token = $this->get('payum')->getHttpRequestVerifier()->verify($request);

        $gateway = $this->get('payum')->getGateway($token->getGatewayName());

        // or Payum can fetch the model for you while executing a request (Preferred).
        $gateway->execute($status = new GetHumanStatus($token));
        $payment = $status->getFirstModel();

        $reservation->setStatut($reservation::STATUS_PAYER);
        $this->getDoctrine()->getManager()->persist($reservation);
        $this->getDoctrine()->getManager()->flush();

        $billets = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('LouvreBilletterieBundle:Billet')
            ->recupReservation($reservation->getId())
        ;

        $pdfPath = __DIR__.'/../../../../web/upload/reservation_numero_'.$reservation->getId().'.pdf';
        $imagePath = __DIR__.'/../../../../web/bundles/louvrebilletterie/image/';

        $this->get('knp_snappy.pdf')->generateFromHtml(
            $this->renderView('@LouvreBilletterie/pdfBillet.html.twig',array(
                'reservation' => $reservation,
                'billets' => $billets
                )),$pdfPath);

        $email =  \Swift_Message::newInstance()
                ->setSubject('Test')
                ->setFrom('Louvre@test.com')
                ->setTo('p_bordmann@orange.fr')
                ->setContentType('text/html')
        ;

        /* @var \Swift_Message $email */
        $email  ->attach(\Swift_Attachment::fromPath($pdfPath));
        $image = $email->embed(\Swift_Image::fromPath($imagePath.'louvre_logo_frise.png'));

        /* @var \Swift_Mime_MimePart $email */
        $email->setBody($this->renderView('@LouvreBilletterie/emailBillet.html.twig'
            ,array(
                'reservation' => $reservation,
                'logo' => $image))
        );

        /* @var \Swift_Message $email */
        $this->get('mailer')->send($email);

        return $this->redirect($this->generateUrl('louvre_billetterie_achat_paiement', ['id' => $payment->getReservation()->getId()]).'#confirmation');
    }
}