<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RaceCompetitor
 *
 * @ORM\Table(name="race_competitor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaceCompetitorRepository")
 */
class RaceCompetitor extends AbstractEntity
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Race",inversedBy="competitors", cascade={"persist"})
     */
    private $race;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Competitor",inversedBy="races", cascade={"persist"})
     */
    private $competitor;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer", nullable=true)
     */
    private $number;

    /**
     * @var int
     *
     * @ORM\Column(name="ranck", type="integer", nullable=true)
     */
    private $ranck;

    /**
     * @var int
     *
     * @ORM\Column(name="ranck_category", type="integer", nullable=true)
     */
    private $ranckCategory;

    /**
     * @var Integer
     *
     * @ORM\Column(name="chrono", type="integer", nullable=true)
     */
    private $chrono;

    /**
     * @var string
     *
     * @ORM\Column(name="chrono_string", type="string", nullable=true)
     */
    private $chronoString;

    /**
     * @var int
     *
     * @ORM\Column(name="points", type="integer", nullable=true)
     */
    private $points;

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
     * Set number
     *
     * @param integer $number
     *
     * @return RaceCompetitor
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set ranck
     *
     * @param integer $ranck
     *
     * @return RaceCompetitor
     */
    public function setRanck($ranck)
    {
        $this->ranck = $ranck;

        return $this;
    }

    /**
     * Get ranck
     *
     * @return integer
     */
    public function getRanck()
    {
        return $this->ranck;
    }

    /**
     * Set ranckCategory
     *
     * @param integer $ranckCategory
     *
     * @return RaceCompetitor
     */
    public function setRanckCategory($ranckCategory)
    {
        $this->ranckCategory = $ranckCategory;

        return $this;
    }

    /**
     * Get ranckCategory
     *
     * @return integer
     */
    public function getRanckCategory()
    {
        return $this->ranckCategory;
    }

    /**
     * Set chrono
     *
     * @param \DateTime $chrono
     *
     * @return RaceCompetitor
     */
    public function setChrono($chrono)
    {
        $this->chrono = $chrono;

        return $this;
    }

    /**
     * Get chrono
     *
     * @return \string
     */
    public function getChronoString()
    {
        return $this->chronoString;
    }

    /**
     * Set chrono
     *
     * @param \DateTime $chrono
     *
     * @return RaceCompetitor
     */
    public function setChronoString($chronoString)
    {
        $this->chronoString = $chronoString;

        return $this;
    }

    /**
     * Get chrono
     *
     * @return \DateTime
     */
    public function getChrono()
    {
        return $this->chrono;
    }

    /**
     * Set race
     *
     * @param \AppBundle\Entity\Race $race
     *
     * @return RaceCompetitor
     */
    public function setRace(\AppBundle\Entity\Race $race = null)
    {
        $this->race = $race;

        return $this;
    }

    /**
     * Get race
     *
     * @return \AppBundle\Entity\Race
     */
    public function getRace()
    {
        return $this->race;
    }

    /**
     * Set competitor
     *
     * @param \AppBundle\Entity\Competitor $competitor
     *
     * @return RaceCompetitor
     */
    public function setCompetitor(\AppBundle\Entity\Competitor $competitor = null)
    {
        $this->competitor = $competitor;

        //$competitor->addRace($this->race);
        return $this;
    }

    /**
     * Get competitor
     *
     * @return \AppBundle\Entity\Competitor
     */
    public function getCompetitor()
    {
        return $this->competitor;
    }

    /**
     * Set points
     *
     * @param integer $points
     *
     * @return RaceCompetitor
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer
     */
    public function getPoints()
    {
        return $this->points;
    }

}
