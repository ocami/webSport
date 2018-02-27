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
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="km", type="integer", nullable=true)
     */
    private $km;

    /**
     * @var Date
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var Time
     *
     * @ORM\Column(name="time", type="time", nullable=true)
     */
    private $time;

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
     */
    private $competitorCanEntry;


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
     * Set km
     *
     * @param integer $km
     *
     * @return Race
     */
    public function setKm($km)
    {
        $this->km = $km;

        return $this;
    }

    /**
     * Get km
     *
     * @return integer
     */
    public function getKm()
    {
        return $this->km;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Race
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
     * Set time
     *
     * @param \DateTime $time
     *
     * @return Race
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
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
     * Set competitorCanEntry
     *
     * @param boolean $competitorCanEntry
     *
     * @return Race
     */
    public function setCompetitorCanEntry($competitorCanEntry)
    {
        $this->competitorCanEntry = $competitorCanEntry;

        return $this;
    }

    /**
     * Get competitorCanEntry
     */
    public function getCompetitorCanEntry()
    {
        return $this->competitorCanEntry;
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
}
