<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 22:14
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * competition
 *
 * @ORM\Table(name="competition")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompetitionRepository")
 */
class Competition
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
     * @var string
     * @ORM\Column(name="code", type="string", length=20, unique=true, nullable=true, unique=true)
     */
    private $code;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text", length=255, unique=false, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $adress;

    /**
     * @var int
     *
     * @ORM\Column(name="dep", type="integer")
     */
    private $dep;

    /**
     * @var Date
     *
     * @ORM\Column(name="dateStart", type="date", nullable=true)
     */
    private $dateStart;

    /**
     * @var Date
     *
     * @ORM\Column(name="dateEnd", type="date", nullable=true)
     */
    private $dateEnd;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organizer", inversedBy ="competitions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organizer;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Race", mappedBy="competition")
     */
    private $races;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Set code
     *
     * @param string $code
     *
     * @return Competition
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

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Competition
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Competition
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set adress
     *
     * @param string $adress
     *
     * @return Competition
     */
    public function setAdress($adress)
    {
        $this->adress = $adress;

        return $this;
    }

    /**
     * Get adress
     *
     * @return string
     */
    public function getAdress()
    {
        return $this->adress;
    }

    /**
     * Set dep
     *
     * @param integer $dep
     *
     * @return Competition
     */
    public function setDep($dep)
    {
        $this->dep = $dep;

        return $this;
    }

    /**
     * Get dep
     *
     * @return integer
     */
    public function getDep()
    {
        return $this->dep;
    }

    /**
     * Set dateStart
     *
     * @param \DateTime $dateStart
     *
     * @return Competition
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     *
     * @return Competition
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set organizer
     *
     * @param \AppBundle\Entity\Organizer $organizer
     *
     * @return Competition
     */
    public function setOrganizer(\AppBundle\Entity\Organizer $organizer)
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * Get organizer
     *
     * @return \AppBundle\Entity\Organizer
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }

    /**
     * Add race
     *
     * @param \AppBundle\Entity\Race $race
     *
     * @return Competition
     */
    public function addRace(\AppBundle\Entity\Race $race)
    {
        $this->races[] = $race;

        return $this;
    }

    /**
     * Remove race
     *
     * @param \AppBundle\Entity\Race $race
     */
    public function removeRace(\AppBundle\Entity\Race $race)
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
     * Set description
     *
     * @param string $description
     *
     * @return Competition
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
