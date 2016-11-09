<?php
// src BilletterieBundle\Validator\Constraints\PlafondJournalierBillets.php

namespace Louvre\BilletterieBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PlafondJournalierBillets extends Constraint
{
    public $message = "Le plafond journalier de billets est dépassé, veuillez sélectionner une autre date !";

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
