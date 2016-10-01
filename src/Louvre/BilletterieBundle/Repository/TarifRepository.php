<?php

namespace Louvre\BilletterieBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TarifRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TarifRepository extends EntityRepository
{
    public function selectionTarif($type) {
        $qb = $this->createQueryBuilder('s')
                   ->select('s.id', 's.nom', 's.tarif')
                   ->where('s.nom = :pattern')
                   ->setParameter('pattern', $type)
        ;

        return $qb->getQuery()->getArrayResult();
    }
}
