<?php
//src Louvre\BilletterieBundle\Validator\Constraints\HoraireMaxPM.php

namespace Louvre\BilletterieBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HoraireMaxPM extends Constraint
{
    public $message = "Vous ne pouvez choisir un billet Journée après 14H !";

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}