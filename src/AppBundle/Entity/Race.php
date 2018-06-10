<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 22:12
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Time;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * race
 *
 * @ORM\Table(name="race")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaceRepository")
 */
class Race
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\Length(max=50)
     * @Assert\Regex("/^([a-zA-Z0-9_-àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ ]){5,50}$/")
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="distance", type="float", nullable=true)
     *
     * @Assert\Regex("/^[0-9]+(\.[0-9][0-9]?)?$/")
     *
     */
    private $distance;

    /**
     *
     * @var string
     * @ORM\Column(name="date_time", type="string", length=255)
     *
     */
    private $dateTime;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Competition", inversedBy ="races")
     * @ORM\JoinColumn(nullable=false)
     */
    private $competition;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Championship", inversedBy ="races")
     */
    private $championships;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Category", inversedBy ="races")
     */
    private $categories;

    /**
     * @var string
     *
     * @ORM\Column(name="categories_string", type="string")
     */
    private $categoriesString;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RaceCompetitor",mappedBy="race")
     */
    private $competitors;

    /**
     * @var Boolean
     * @ORM\Column(name="in_championship", type="boolean")
     */
    private $inChampionship = false;

    /**
     * @var Boolean
     * @ORM\Column(name="request_in_championship", type="boolean")
     */
    private $requestInChampionship = false;

    /**
     * @var Boolean
     * @ORM\Column(name="enrol", type="boolean")
     */
    private $enrol = true;

    /**
     * @var Boolean
     * @ORM\Column(name="passed", type="boolean")
     */
    private $passed = false;

    /**
     * @var Boolean
     * @ORM\Column(name="supervised", type="boolean")
     */
    private $supervised = false;

    /**
     * @var Boolean
     * @ORM\Column(name="valid", type="boolean")
     */
    private $valid = false;

    /**
     * @var Boolean
     * @ORM\Column(name="isOrganizer", type="boolean")
     */
    private $isOrganizer = false;

    /**
     * @var int
     *
     * 0 : can't register
     * 1 : can register
     * 2 : is register
     *
     */
    private $competitorRegister;

    /**
     * @var Boolean
     */
    private $fullCat = false;

    /**
     * @var int
     *
     * @ORM\Column(name="state", type="integer")
     * 
     * 1 : open enrol
     * 2 : close enrol
     * 3 : passed / ranck
     * 
     */
    private $state = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->championships = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Race
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
     * @return Race
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
     * Set distance
     *
     * @param float $distance
     *
     * @return Race
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return integer
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set dateTime
     *
     * @param string $dateTime
     *
     * @return Race
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
        return $this;
    }

    /**
     * Get dateTime
     *
     * @return string
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set competition
     *
     * @param \AppBundle\Entity\Competition $competition
     *
     * @return Race
     */
    public function setCompetition(\AppBundle\Entity\Competition $competition)
    {
        $this->competition = $competition;

        return $this;
    }

    /**
     * Get competition
     *
     * @return \AppBundle\Entity\Competition
     */
    public function getCompetition()
    {
        return $this->competition;
    }

    /**
     * Add championship
     *
     * @param \AppBundle\Entity\Championship $championship
     *
     * @return Race
     */
    public function addChampionship(\AppBundle\Entity\Championship $championship)
    {
        $this->championships[] = $championship;
        $championship->addRace($this);
        return $this;
    }

    /**
     * Remove championship
     *
     * @param \AppBundle\Entity\Championship $championship
     */
    public function removeChampionship(\AppBundle\Entity\Championship $championship)
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
     * Set categoriesString
     *
     * @param string $categoriesString
     *
     * @return Race
     */
    public function setCategoriesString($categoriesString)
    {
        $this->categoriesString = $categoriesString;
        return $this;
    }

    /**
     * Get categoriesString
     *
     * @return string
     */
    public function getCategoriesString()
    {
        return $this->categoriesString;
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
        $category->addRace($this);
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
     * Add competitor
     *
     * @param \AppBundle\Entity\RaceCompetitor $competitor
     *
     * @return Race
     */
    public function addCompetitor(\AppBundle\Entity\RaceCompetitor $competitor)
    {
        $this->competitors[] = $competitor;

        return $this;
    }

    /**
     * Remove competitor
     *
     * @param \AppBundle\Entity\RaceCompetitor $competitor
     */
    public function removeCompetitor(\AppBundle\Entity\RaceCompetitor $competitor)
    {
        $this->competitors->removeElement($competitor);
    }

    /**
     * Get competitors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompetitors()
    {
        return $this->competitors;
    }

    /**
     * Set competitorRegister
     *
     * @param int $competitorRegister
     *
     * @return Race
     */
    public function setCompetitorRegister($competitorRegister)
    {
        $this->competitorRegister = $competitorRegister;

        return $this;
    }

    /**
     * Get competitorRegister
     */
    public function getCompetitorRegister()
    {
        return $this->competitorRegister;
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
     * Set requestInChampionship
     *
     * @param boolean $requestInChampionship
     *
     * @return Race
     */
    public function setRequestInChampionship($requestInChampionship)
    {
        $this->requestInChampionship = $requestInChampionship;

        return $this;
    }

    /**
     * Get requestInChampionship
     *
     * @return boolean
     */
    public function getRequestInChampionship()
    {
        return $this->requestInChampionship;
    }

    /**
     * Set enrol
     *
     * @param boolean $enrol
     *
     * @return Race
     */
    public function setEnrol($enrol)
    {
        $this->enrol = $enrol;

        return $this;
    }

    /**
     * Get enrol
     *
     * @return boolean
     */
    public function getEnrol()
    {
        return $this->enrol;
    }

    /**
     * Set passed
     *
     * @param boolean $passed
     *
     * @return Race
     */
    public function setPassed($passed)
    {
        $this->passed = $passed;

        return $this;
    }

    /**
     * Get passed
     *
     * @return boolean
     */
    public function getPassed()
    {
        return $this->passed;
    }

    /**
     * Set supervised
     *
     * @param boolean $supervised
     *
     * @return Race
     */
    public function setSupervised($supervised)
    {
        $this->supervised = $supervised;

        return $this;
    }

    /**
     * Get supervised
     *
     * @return boolean
     */
    public function getSupervised()
    {
        return $this->supervised;
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
     * Set fullCat
     *
     * @param boolean $fullCat
     *
     * @return Race
     */
    public function setFullCat($fullCat)
    {
        $this->fullCat = $fullCat;

        return $this;
    }

    /**
     * Get fullCat
     *
     * @return boolean
     */
    public function getFullCat()
    {
        return $this->fullCat;
    }

    /**
     * Set isOrganizer
     *
     * @param boolean $isOrganizer
     *
     * @return Race
     */
    public function setIsOrganizer($isOrganizer)
    {
        $this->isOrganizer = $isOrganizer;

        return $this;
    }

    /**
     * Get isOrganizer
     *
     * @return boolean
     */
    public function getIsOrganizer()
    {
        return $this->isOrganizer;
    }

    /**
     * Set state
     *
     * @param int $state
     *
     * @return Race
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }
}
