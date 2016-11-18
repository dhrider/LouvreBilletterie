<?php

namespace Louvre\BilletterieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Louvre\BilletterieBundle\Validator\Constraints\HoraireMaxPM;

/**
 * Billet
 *
 * @ORM\Table(name="billet")
 * @ORM\Entity(repositoryClass="Louvre\BilletterieBundle\Repository\BilletRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Billet
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @var string
     * @ORM\Column(name="nom", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nom;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @var string
     * @ORM\Column(name="prenom", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $prenom;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @var string
     * @ORM\Column(name="pays", type="string", length=255)
     */
    private $pays;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @var \DateTime
     * @ORM\Column(name="date_naissance", type="date")
     * @Assert\Date()
     */
    private $dateNaissance;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=255)
     * @Assert\NotBlank()
     * @HoraireMaxPM
     */
    private $type;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @var bool
     * @ORM\Column(name="reduit", type="boolean")
     */
    private $reduit;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Tarif", inversedBy="billets")
     */
    private $tarif;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @var int
     * @ORM\Column(name="montant", type="integer")
     */
    private $montant;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Louvre\BilletterieBundle\Entity\Reservation", inversedBy="billets")
     */
    private $reservation;

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @return mixed
     */
    public function getReservation()
    {
        return $this->reservation;
    }


    /**
     * @param mixed $reservation
     * @return Billet
     */
    public function setReservation(Reservation $reservation)
    {
        $this->reservation = $reservation;


        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @return int
     */
    public function getMontant()
    {
        return $this->montant;
    }


    /**
     * @param int $montant
     */
    public function setMontant($montant)
    {
        if ($this->getType() == 'demiJournee') {
            $this->montant = $montant / 2;
        }
        else {
            $this->montant = $montant;
        }

    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @return mixed
     */
    public function getReduit()
    {
        return $this->reduit;
    }


    /**
     * @param mixed $reduit
     */
    public function setReduit($reduit)
    {
        $this->reduit = $reduit;
    }

    ////////////////////////////////////////////////////////////////////////////
    
    /**
     * Get id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Set nom
     * @param string $nom
     * @return Billet
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }


    /**
     * Get nom
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Set prenom
     * @param string $prenom
     * @return Billet
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }


    /**
     * Get prenom
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Set pays
     * @param string $pays
     * @return Billet
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }


    /**
     * Get pays
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Set dateNaissance
     * @param \DateTime $dateNaissance
     * @return Billet
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }


    /**
     * Get dateNaissance
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
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
}

