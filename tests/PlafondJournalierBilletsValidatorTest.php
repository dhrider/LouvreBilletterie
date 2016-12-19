<?php

use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Louvre\BilletterieBundle\Repository\BilletRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Louvre\BilletterieBundle\Validator\Constraints\PlafondJournalierBilletsValidator;
use Louvre\BilletterieBundle\Validator\Constraints\PlafondJournalierBillets;

class PlafondJournalierBilletsValidatorTest extends AbstractConstraintValidatorTest
{
    protected $billetRepository;

    public function setUp()
    {
        $this->billetRepository = $this->getMockBuilder(BilletRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        parent::setUp();
    }


    public function createValidator()
    {
        $registry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $registry->method('getRepository')->willReturn($this->billetRepository);

        $validator = new PlafondJournalierBilletsValidator($registry);

        return $validator;
    }

    public function testPlafondBilletDepasse()
    {
        $this->billetRepository
            ->method('nombreBilletsPourUneDate')
            ->willReturn([1 => 1001])
        ;

        $this->validator->validate('10-10-2016', new PlafondJournalierBillets());

        $this->assertEquals(1, count($this->context->getViolations()));
    }

    public function testPlafondBilletsNonDepasse()
    {
        $this->billetRepository
            ->method('nombreBilletsPourUneDate')
            ->willReturn([1 => 999])
        ;

        $this->validator->validate('10-10-2016', new PlafondJournalierBillets());

        $this->assertEquals(0, count($this->context->getViolations()));
    }
}