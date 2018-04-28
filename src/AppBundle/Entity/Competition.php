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
     * Constructor
     */
    public function __construct()
    {
        $this->races = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }


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
     *
     * @Assert\Length(min=5)
     * @Assert\Length(max=50)
     * @Assert\Regex("/^([a-zA-Z0-9_-àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ ]){5,50}$/")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="dateStart", type="string", nullable=true)
     * @Assert\Regex("/(19[5-9][0-9]|20[0-4][0-9]|2050)[-](0?[1-9]|1[0-2])[-](0?[1-9]|[12][0-9]|3[01])/")
     * @Assert\Length(max=10)
     * @Assert\Length(min=10)
     */
    private $dateStart;

    /**
     * @var string
     * @ORM\Column(name="dateEnd", type="string", nullable=true)
     * @Assert\Regex("/(19[5-9][0-9]|20[0-4][0-9]|2050)[-](0?[1-9]|1[0-2])[-](0?[1-9]|[12][0-9]|3[01])/")
     * @Assert\Length(max=10)
     * @Assert\Length(min=10)
     */
    private $dateEnd;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location", inversedBy ="competitions", cascade="persist")
     * @ORM\JoinColumn(nullable=false)
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="locationString", type="string", length=1000)
     * @Assert\Length(min=5)
     *
     */
    private $locationString;

    /**
     * @var Boolean
     * @ORM\Column(name="valid", type="boolean")
     */
    private $valid = false;

    /**
     * @var Boolean
     * @ORM\Column(name="in_championship", type="boolean")
     */
    private $inChampionship = false;

    /**
     * @var Boolean
     */
    private $competitorCanRegister = false;

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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Category", inversedBy ="competitions")
     */
    private $categories;



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
     * Set location
     *
     * @param \AppBundle\Entity\Location $location
     *
     * @return Competition
     */
    public function setLocation(\AppBundle\Entity\Location $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \AppBundle\Entity\Location
     */
    public function getLocation()
    {
        return $this->location;
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

    /**
     * Add category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return Race
     */
    public function addCategory(\AppBundle\Entity\Category $category)
    {
        $this->categories[] = $category;
        $category->addCompetition($this);
        return $this;
    }

    /**
     * Remove category
     *
     * @param \AppBundle\Entity\Category $category
     */
    public function removeCategory(\AppBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set inChampionship
     *
     * @param boolean $inChampionship
     *
     * @return Race
     */
    public function setInChampionship($inChampionship)
    {
        $this->inChampionship = $inChampionship;

        return $this;
    }

    /**
     * Get inChampionship
     *
     * @return boolean
     */
    public function getInChampionship()
    {
        return $this->inChampionship;
    }

    /**
     * Set valid
     *
     * @param boolean $valid
     *
     * @return Race
     */
    public function setValid($valid)
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Get valid
     *
     * @return boolean
     */
    public function getValid()
    {
        return $this->valid;
    }

    /**
     * Set locationString
     *
     * @param string $locationString
     *
     * @return Competition
     */
    public function setLocationString($locationString)
    {
        $this->locationString = $locationString;

        return $this;
    }

    /**
     * Get locationString
     *
     * @return string
     */
    public function getLocationString()
    {
        return $this->locationString;
    }

    /**
     * Set competitorCanRegister
     *
     * @param boolean $competitorCanRegister
     *
     * @return Race
     */
    public function setCompetitorCanRegister($competitorCanRegister)
    {
        $this->competitorCanRegister = $competitorCanRegister;

        return $this;
    }

    /**
     * Get competitorCanRegister
     *
     * @return boolean
     */
    public function getCompetitorCanRegister()
    {
        return $this->competitorCanRegister;
    }

}
