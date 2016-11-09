<?php

namespace Louvre\BilletterieBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ReservationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReservationRepository extends EntityRepository
{
    public function recupReservation($id) {
        $qb = $this->createQueryBuilder('r')
            ->select('r')
            ->where('r.id = :pattern')
            ->setParameter('pattern', $id)
        ;

        return $qb->getQuery()->getArrayResult();
    }

    public function nombreBillets($date) {
        return 999;
    }
}
