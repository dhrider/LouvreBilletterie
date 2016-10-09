<?php

namespace Louvre\BilletterieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Reservation
 * @ORM\Table(name="reservation")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Reservation 
{
    const STATUTS = "du";
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="statut", type="string")
     */
    private $statut;

    /**
     * @var \DateTime
     * @ORM\Column(name="dateVisite", type="datetime")
     */
    private $dateVisite;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Billet", cascade={"persist"}, mappedBy="reservation")
     */
    protected $billets;

    /**
     * @var int
     *
     * @ORM\Column(name="total", type="integer")
     */
    private $total;

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @ORM\PrePersist
     */
    public function setTotal()
    {
        $this->total = $this->getMontantTotal();
    }

    public function __construct()
    {
        $this->statut = self::STATUTS;
        $this->billets = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * @param string $statut
     * @return Reservation
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateVisite()
    {
        return $this->dateVisite;
    }

    /**
     * @param \DateTime $dateVisite
     * @return Reservation
     */
    public function setDateVisite(\DateTime $dateVisite)
    {
        $this->dateVisite = $dateVisite;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getBillets()
    {
        return $this->billets;
    }

    /**
     * @param ArrayCollection $billets
     */
    public function setBillets($billets)
    {
        foreach ($billets as $billet) {
            $this->addBillet($billet);
        }

    }

    public function addBillet(Billet $billet)
    {
        $billet->setReservation($this);
        $this->billets[] = $billet;
    }

    public function deleteBillet(Billet $billet)
    {
        $this->billets->removeElement($billet);
    }

    public function getMontantTotal()
    {
        $totalReservation = 0;

        foreach ($this->billets as $billet) {
            $totalReservation += $billet->getMontant(); // pour additionner les montants
        }
        return $totalReservation;
    }
}