<?php

namespace Louvre\BilletterieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tarif
 *
 * @ORM\Table(name="tarif")
 * @ORM\Entity(repositoryClass="Louvre\BilletterieBundle\Repository\TarifRepository")
 */
class Tarif
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @var
     * @ORM\OneToMany(targetEntity="Billet", mappedBy="tarif")
     */
    private $billets;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @var int
     *
     * @ORM\Column(name="tarif", type="integer")
     */
    private $tarif;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @return mixed
     */
    public function getBillets()
    {
        return $this->billets;
    }


    /**
     * @param mixed $billets
     */
    public function setBillets($billets)
    {
        $this->billets = $billets;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }


    /**
     * @param string $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @return int
     */
    public function getTarif()
    {
        return $this->tarif;
    }


    /**
     * @param int $tarif
     */
    public function setTarif($tarif)
    {
        $this->tarif = $tarif;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}

