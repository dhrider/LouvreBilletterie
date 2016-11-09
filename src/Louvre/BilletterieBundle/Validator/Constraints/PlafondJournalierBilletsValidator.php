<?php

namespace Louvre\BilletterieBundle\Validator\Constraints;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class PlafondJournalierBilletsValidator extends ConstraintValidator {

    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {
        $nbReservations = $this->registry->getRepository('LouvreBilletterieBundle:Reservation')->nombreReservation($value);

        $nbBilletsTotal = 0;

        foreach ($nbReservations as $nbReservation) {
            foreach ($nbReservation->getBillets() as $nbBillets){
                $nbBilletsTotal++;
            }
        }

        if ($nbBilletsTotal >= 1000) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}