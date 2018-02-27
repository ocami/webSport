<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 22:12
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="competitor")
 */
class Competitor
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
     * @ORM\Column(name="user_id", type="integer")
     *
     */
    private $userId;

    /**
     * @var string
     * @ORM\Column(name="code", type="string", length=20, unique=true, nullable=true, unique=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="FirstName", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="LastName", type="string", length=255)
     */
    private $lastName;


    /**
     * @var string
     *
     * @ORM\Column(name="sexe", type="string", length=2)
     */
    private $sexe;

    /**
     * @var \Date
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ChampionshipCompetitor",mappedBy="competitor")
     */
    private $championships;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RaceCompetitor", mappedBy="competitor")
     */
    private $races;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->championships = new \Doctrine\Common\Collections\ArrayCollection();
        $this->races = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Competitor
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Competitor
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Competitor
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set sexe
     *
     * @param string $sexe
     *
     * @return Competitor
     */
    public function setSexe($sexe)
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * Get sexe
     *
     * @return string
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Competitor
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Add championship
     *
     * @param \AppBundle\Entity\ChampionshipCompetitor $championship
     *
     * @return Competitor
     */
    public function addChampionship(\AppBundle\Entity\ChampionshipCompetitor $championship)
    {
        $championship->setCompetitor($this);
        $this->championships[] = $championship;

        return $this;
    }

    /**
     * Remove championship
     *
     * @param \AppBundle\Entity\ChampionshipCompetitor $championship
     */
    public function removeChampionship(\AppBundle\Entity\ChampionshipCompetitor $championship)
    {
        $this->championships->removeElement($championship);
    }

    /**
     * Get championships
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChampionships()
    {
        return $this->championships;
    }



    /**
     * Add race
     *
     * @param \AppBundle\Entity\RaceCompetitor $race
     *
     * @return Competitor
     */
    public function addRace(\AppBundle\Entity\RaceCompetitor $race)
    {
        $this->races[] = $race;

        return $this;
    }

    /**
     * Remove race
     *
     * @param \AppBundle\Entity\RaceCompetitor $race
     */
    public function removeRace(\AppBundle\Entity\RaceCompetitor $race)
    {
        $this->races->removeElement($race);
    }

    /**
     * Get races
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRaces()
    {
        return $this->races;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Competitor
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
