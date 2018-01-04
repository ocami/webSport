<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 22:13
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\categoryRepository")
 */
class Category
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
     *
     * @ORM\Column(name="sexe", type="string", length=2)
     */
    private $sexe;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=10, unique=true, nullable=false, unique=true)
     */
    private $code;

    /**
     * @var int
     *
     * @ORM\Column(name="create_by", type="integer", nullable=true)
     */
    private $createBy;

    /**
     * @var int
     *
     * @ORM\Column(name="age_max", type="integer")
     */
    private $ageMax;

    /**
     * @var int
     *
     * @ORM\Column(name="age_min", type="integer")
     */
    private $ageMin;


    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Championship",inversedBy="category", cascade={"persist"})
     */
    private $championship;


    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Race", inversedBy ="categories")
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
     * Set sexe
     *
     * @param string $sexe
     *
     * @return Category
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
     * Set name
     *
     * @param string $name
     *
     * @return Category
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
     * Set code
     *
     * @param string $code
     *
     * @return Category
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
     * Set createBy
     *
     * @param integer $createBy
     *
     * @return Category
     */
    public function setCreateBy($createBy)
    {
        $this->createBy = $createBy;

        return $this;
    }

    /**
     * Get createBy
     *
     * @return integer
     */
    public function getCreateBy()
    {
        return $this->createBy;
    }

    /**
     * Set ageMax
     *
     * @param integer $ageMax
     *
     * @return Category
     */
    public function setAgeMax($ageMax)
    {
        $this->ageMax = $ageMax;

        return $this;
    }

    /**
     * Get ageMax
     *
     * @return integer
     */
    public function getAgeMax()
    {
        return $this->ageMax;
    }

    /**
     * Set ageMin
     *
     * @param integer $ageMin
     *
     * @return Category
     */
    public function setAgeMin($ageMin)
    {
        $this->ageMin = $ageMin;

        return $this;
    }

    /**
     * Get ageMin
     *
     * @return integer
     */
    public function getAgeMin()
    {
        return $this->ageMin;
    }

    /**
     * Set championship
     *
     * @param \AppBundle\Entity\Championship $championship
     *
     * @return Category
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
     * Add race
     *
     * @param \AppBundle\Entity\Race $race
     *
     * @return Category
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
}
