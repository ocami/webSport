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
 * @ORM\Table(name="competitor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompetitorRepository")
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
     * @var string
     * @ORM\Column(name="date", type="string")
     * @Assert\Regex("/(19[5-9][0-9]|20[0-4][0-9]|2050)[-](0?[1-9]|1[0-2])[-](0?[1-9]|[12][0-9]|3[01])/")
     * @Assert\Length(max=10)
     * @Assert\Length(min=10)
     */
    private $date;

    /**
 * @var int
 *
 */
    private $age;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="integer")
     *
     */
    private $level;
    
    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\ChampionshipCompetitor",mappedBy="competitor")
     */
    private $championship;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RaceCompetitor", mappedBy="competitor")
     */
    private $races;

    /**
     * @var Category
     */
    private $category;

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
     * Set level
     *
     * @param integer $level
     *
     * @return Competitor
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
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
     * @param string $date
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
     * @return string
     */
    public function getDate()
    {
        return $this->date;
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

    /**
     * Set category
     *
     * @param string $category
     *
     * @return Competitor
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set championship
     *
     * @param string $championship
     *
     * @return Competitor
     */
    public function setChampionship($championship)
    {
        $this->championship = $championship;

        return $this;
    }

    /**
     * Get championship
     *
     * @return string
     */
    public function getChampionship()
    {
        return $this->championship;
    }

    /**
     * Get age
     *
     * @return int
     */
    public function getAge()
    {
        $today = date("Y-m-d");
        $competitorDate = $this->getDateObject();

        $diff = date_diff($competitorDate, date_create($today));

        return $diff->format('%y');
    }

    /**
     * Get DateObject
     *
     * @return \DateTime
     */
    public function getDateObject()
    {
        return new \DateTime($this->date);

    }
}
