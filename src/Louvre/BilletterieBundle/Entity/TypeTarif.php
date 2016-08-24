<?php

namespace Louvre\BilletterieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeTarif
 *
 * @ORM\Table(name="type_tarif")
 * @ORM\Entity(repositoryClass="Louvre\BilletterieBundle\Repository\TypeTarifRepository")
 */
class TypeTarif
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Louvre\BilletterieBundle\Entity\TypeBillet")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idTypeBillet;

    /**
     * @ORM\ManyToOne(targetEntity="Louvre\BilletterieBundle\Entity\Tarif")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idTarif;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    public function setIdTypeBillet(TypeBillet $idTypeBillet)
    {
        $this->idTypeBillet = $idTypeBillet;

        return $this;
    }

    /**
     * Get idTypeBillet
     *
     * @return int
     */
    public function getIdTypeBillet()
    {
        return $this->idTypeBillet;
    }


    public function setIdTarif(Tarif $idTarif)
    {
        $this->idTarif = $idTarif;

        return $this;
    }

    /**
     * Get idTarif
     *
     * @return int
     */
    public function getIdTarif()
    {
        return $this->idTarif;
    }
}

