<?php
// src BilletterieBundle\Validator\Constraints\PlafondJournalierBilletsValidator.php

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
        $nbBilletsTotal = $this->registry->getRepository('LouvreBilletterieBundle:Billet')->nombreBilletsPourUneDate($value);


        if ($nbBilletsTotal >= 2) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}