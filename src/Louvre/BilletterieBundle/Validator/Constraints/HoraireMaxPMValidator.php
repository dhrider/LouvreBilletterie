<?php
// src BilletterieBundle\Validator\Constraints\HoraireMaxPMValidatorValidator.php

namespace Louvre\BilletterieBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class HoraireMaxPMValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $heure = strftime('%H');

        if ($value === 'journee' && (int)$heure >= 10)
        {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}