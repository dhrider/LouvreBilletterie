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

        $reservation->setStatut($reservation::STATUS_PAYER);

        $this->getDoctrine()->getManager()->persist($reservation);
        $this->getDoctrine()->getManager()->flush();

        $pdfPath = __DIR__.'/../../../../web/upload/reservation_'.$reservation->getId().'.pdf';
        $imagePath = __DIR__.'/../../../../web/bundles/louvrebilletterie/image/';

        $pdfHtml = $this->renderView('@LouvreBilletterie/pdfBillet.html.twig',array(
            'reservation' => $reservation,
            'billets' => $reservation->getBillets()
        ));

        $this->get('knp_snappy.pdf')->generateFromHtml($pdfHtml,$pdfPath);

        /* @var \Swift_Message $email */
        $email =  \Swift_Message::newInstance()
                ->setSubject('Test')
                ->setFrom('Louvre@test.com')
                ->setTo('p_bordmann@orange.fr')
                ->setContentType('text/html')
        ;

        $image = $email->embed(\Swift_Image::fromPath($imagePath.'louvre_logo_frise.png'));

        $email->attach(\Swift_Attachment::fromPath($pdfPath))
              ->setBody($this->renderView('@LouvreBilletterie/emailBillet.html.twig',
                        array('reservation' => $reservation,'logo' => $image))
        );

        $this->get('mailer')->send($email);

        return $this->redirect($this->generateUrl('louvre_billetterie_achat_paiement',
            ['id' => $payment->getReservation()->getId()]).'#confirmation');
    }
}