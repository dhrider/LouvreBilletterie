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

    // on vérifié qu'on ne dépasse pas le nombre total de billets pour la date de réservation choisie
    public function validate($value, Constraint $constraint)
    {
        $requete = $this->registry->getRepository('LouvreBilletterieBundle:Billet')->nombreBilletsPourUneDate($value);

        $nbBilletsTotal = (int)$requete[1];

        if ($nbBilletsTotal > $constraint->nombreBilletParJourMax) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}