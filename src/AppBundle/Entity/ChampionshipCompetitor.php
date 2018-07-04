<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * RaceCompetitor
 *
 * @ORM\Table(name="championship_competitor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ChampionshipCompetitorRepository")
 */
class ChampionshipCompetitor
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Championship",inversedBy="competitors",  cascade={"persist"})
     */
    private $championship;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Competitor",inversedBy="championship",  cascade={"persist"})
     */
    private $competitor;

    /**
     * @var int
     *
     * @ORM\Column(name="points", type="integer", nullable=true)
     */
    private $points;

    /**
     * @var int
     *
     * @ORM\Column(name="rank", type="integer", nullable=true)
     */
    private $rank;


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
     * Set points
     *
     * @param integer $points
     *
     * @return ChampionshipCompetitor
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

    /**
     * Set rank
     *
     * @param integer $rank
     *
     * @return ChampionshipCompetitor
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set championship
     *
     * @param \AppBundle\Entity\Championship $championship
     *
     * @return ChampionshipCompetitor
     */
    public function setChampionship(\AppBundle\Entity\Championship $championship = null)
    {
        $this->championship = $championship;

        return $this;
    }

    /**
     * Get championship
     *
     * @return \AppBundle\Entity\Championship
     */
    public function getChampionship()
    {
        return $this->championship;
    }

    /**
     * Set competitor
     *
     * @param \AppBundle\Entity\Competitor $competitor
     *
     * @return ChampionshipCompetitor
     */
    public function setCompetitor(\AppBundle\Entity\Competitor $competitor = null)
    {
        $this->competitor = $competitor;

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
}
