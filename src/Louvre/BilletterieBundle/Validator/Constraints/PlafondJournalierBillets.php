<?php
// src BilletterieBundle\Validator\Constraints\PlafondJournalierBillets.php

namespace BilletterieBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PlafondJournalierBillet extends Constraint
{
    public $message = "Le plafond journalier de billets est dépassé !";
}