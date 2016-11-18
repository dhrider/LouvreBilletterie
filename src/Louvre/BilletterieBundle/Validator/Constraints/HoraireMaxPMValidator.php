<?php
// src BilletterieBundle\Validator\Constraints\HoraireMaxPMValidatorValidator.php

namespace Louvre\BilletterieBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class HoraireMaxPMValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        // on récupère l'heure courant
        $heure = strftime('%H');

        // si le type de billet choisie est journée est qu'il est plus de 14H
        if ($value === 'journee' && ((int)$heure + 1) >= 14)
        {
            // On invalide
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}