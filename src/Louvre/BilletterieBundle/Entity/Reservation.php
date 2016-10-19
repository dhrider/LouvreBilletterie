<?php

namespace Louvre\BilletterieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Reservation
 * @ORM\Table(name="reservation")
 * @ORM\Entity(repositoryClass="Louvre\BilletterieBundle\Repository\ReservationRepository")
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
    private $dateReservation;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Billet", cascade={"persist"}, mappedBy="reservation")
     */
    protected $billets;

    /**
     * @var string
     * @ORM\Column(name="email", type="string")
     */
    private $email;

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
     * @param int $total
     * @ORM\PrePersist
     */
    public function setTotal($total)
    {
        foreach ($this->billets as &$billet) {

            $montant = $billet->getTarif()->getTarif();

            if($montant != 0 && $billet->getType() =='demiJournee') {
                $montant = $montant/2;
            }

            $billet->setMontant($montant);
        }

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
        if (!$this->billets->contains($billet)) {
            $this->billets[] = $billet;
        }
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