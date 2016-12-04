<?php

use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;

class HoraireMaxPMValidatorTest extends AbstractConstraintValidatorTest
{
    protected $heureConstraint;

    public function setUp()
    {
        $this->heureConstraint = $this
            ->getMockBuilder('Louvre\BilletterieBundle\Validator\Constraints\HoraireMaxPM')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        parent::setUp();
    }

    public function getHeure()
    {
        return (int)strftime('%H');
    }

    public function createValidator()
    {
        $validator = new \Louvre\BilletterieBundle\Validator\Constraints\HoraireMaxPMValidator();

        return $validator;
    }

    public function testBilletJourneeAvant14 ()
    {
        $this->heureConstraint->heure = $this->getHeure() + 1;

        $this->validator->validate("journee", $this->heureConstraint);

        $this->assertEquals(0, count($this->context->getViolations()));
    }

    public function testBilletJourneeApres14 ()
    {
        $this->heureConstraint->heure = $this->getHeure() - 1;

        $this->validator->validate("journee", $this->heureConstraint);

        $this->assertEquals(1, count($this->context->getViolations()));
    }

    public function testBilletDemiJourneeAvant14 ()
    {
        $this->heureConstraint->heure = $this->getHeure() + 1;

        $this->validator->validate("demiJournee", $this->heureConstraint);

        $this->assertEquals(0, count($this->context->getViolations()));
    }

    public function testBilletDemiJourneeApres14 ()
    {
        $this->heureConstraint->heure = $this->getHeure() - 1;

        $this->validator->validate("demiJournee", $this->heureConstraint);

        $this->assertEquals(0, count($this->context->getViolations()));
    }
}