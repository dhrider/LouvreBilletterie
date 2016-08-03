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
     * @var int
     *
     * @ORM\Column(name="id_type_billet", type="integer")
     */
    private $idTypeBillet;

    /**
     * @var int
     *
     * @ORM\Column(name="id_tarif", type="integer")
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

    /**
     * Set idTypeBillet
     *
     * @param integer $idTypeBillet
     *
     * @return TypeTarif
     */
    public function setIdTypeBillet($idTypeBillet)
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

    /**
     * Set idTarif
     *
     * @param integer $idTarif
     *
     * @return TypeTarif
     */
    public function setIdTarif($idTarif)
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

