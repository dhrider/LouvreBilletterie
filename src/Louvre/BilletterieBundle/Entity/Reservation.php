<?php

namespace Louvre\BilletterieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Louvre\BilletterieBundle\Validator\Constraints as BilletterieAssert;

/**
 * Class Reservation
 * @ORM\Table(name="reservation")
 * @ORM\Entity(repositoryClass="Louvre\BilletterieBundle\Repository\ReservationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Reservation 
{
    const STATUTS_DU = "du";
    const STATUS_PAYER = "payer";

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
     * @ORM\Column(name="statut", type="string")
     */
    private $statut;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @var \DateTime
     * @ORM\Column(name="dateVisite", type="datetime")
     * @BilletterieAssert\PlafondJournalierBillets
     */
    private $dateReservation;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @ORM\OneToMany(targetEntity="Billet", cascade={"all"}, mappedBy="reservation", fetch="EAGER")
     */
    private $billets;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @var string
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @var int
     * @ORM\Column(name="total", type="integer", nullable=false)
     */

    private $total;

    ////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        $this->statut = self::STATUTS_DU;
        $this->billets = new ArrayCollection();
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }


    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }


    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    ////////////////////////////////////////////////////////////////////////////

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

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @return \DateTime
     */
    public function getDateReservation()
    {
        return $this->dateReservation;
    }


    /**
     * @param \DateTime $dateVisite
     * @return Reservation
     */
    public function setDateReservation(\DateTime $dateVisite)
    {
        $this->dateReservation = $dateVisite;
        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////

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

        foreach ($billets as &$billet) {
            $billet->setReservation($this);
        }

        $this->billets = $billets;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @param Billet $billet
     */
    public function addBillet(Billet $billet)
    {

        if (!$this->billets->contains($billet)) {
            $billet->setReservation($this);
            $this->billets[] = $billet;
        }
    }

    ////////////////////////////////////////////////////////////////////////////

    public function removeBillet(Billet $billet){

        $this->billets->removeElement($billet);
    }
}