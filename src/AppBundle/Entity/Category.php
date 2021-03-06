<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 22:13
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Competitor;


/**
 * category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @Assert\Length(min="1", max="2")
     * @ORM\Column(name="gender", type="string", length=2)
     */
    private $gender;

    /**
     * @var string
     * @Assert\Length(min="2", max="255", minMessage="Ce champ doit comporter entre 2 et 255 caractères")
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="code", type="string", length=20, unique=true, nullable=true, unique=true)
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
     * @ORM\Column(name="age_max", type="integer")
     */
    private $ageMax;

    /**
     * @var int
     * @ORM\Column(name="age_min", type="integer")
     */
    private $ageMin;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Competitor", mappedBy ="category")
     */
    private $competitors;
    
    
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Race", mappedBy ="categories")
     */
    private $races;
    

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Competition", mappedBy ="categories")
     */
    private $competitions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->races = new ArrayCollection();
        $this->competitions = new ArrayCollection();
        $this->competitors = new  ArrayCollection();
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
     * Set gender
     *
     * @param string $gender
     *
     * @return category
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return category
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
     * @return category
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
     * @return category
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
     * @return category
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
     * @return category
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
     * Add race
     *
     * @param \AppBundle\Entity\Race $race
     *
     * @return category
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
     * Add competitor
     *
     * @param Competitor $competitor
     *
     * @return category
     */
    public function addCompetitor(Competitor $competitor)
    {
        $this->competitors[] = $competitor;

        return $this;
    }

    /**
     * Remove competitor
     *
     * @param Competitor $competitor
     */
    public function removeCompetitor(Competitor $competitor)
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
     * Add competition
     *
     * @param \AppBundle\Entity\Competition $competition
     *
     * @return category
     */
    public function addCompetition(\AppBundle\Entity\Competition $competition)
    {
        $this->competitions[] = $competition;

        return $this;
    }

    /**
     * Remove competition
     *
     * @param \AppBundle\Entity\Competition $competition
     */
    public function removeCompetition(\AppBundle\Entity\Competition $competition)
    {
        $this->competitions->removeElement($competition);
    }

    /**
     * Get competitions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompetitions()
    {
        return $this->competitions;
    }

    /**
     * @Assert\Callback
     */
    public function isAgeValid(ExecutionContextInterface $context)
    {
        $yearMin = date("Y") - 100;
        $yearMax = date("Y") - 8;

        if ($this->getAgeMin() < $yearMin) {
            // La règle est violée, on définit l'erreur
            $context
                ->buildViolation('Il n\'est pas possible d\'enterigistrer un compétiteur de plus de 100 ans')// message
                ->atPath('ageMin')// attribut de l'objet qui est violé
                ->addViolation() // ceci déclenche l'erreur, ne l'oubliez pas
            ;
        }

        if ($this->getAgeMin() > $yearMax) {
            // La règle est violée, on définit l'erreur
            $context
                ->buildViolation("Il faut être agé de 8 ans minimum pour participer à une course")// message
                ->atPath('ageMin')// attribut de l'objet qui est violé
                ->addViolation() // ceci déclenche l'erreur, ne l'oubliez pas
            ;
        }

        if ($this->getAgeMax() < $yearMin) {
            // La règle est violée, on définit l'erreur
            $context
                ->buildViolation('Il n\'est pas possible d\'enterigistrer un compétiteur de plus de 100 ans')// message
                ->atPath('ageMax')// attribut de l'objet qui est violé
                ->addViolation() // ceci déclenche l'erreur, ne l'oubliez pas
            ;
        }

        if ($this->getAgeMax() > $yearMax) {
            // La règle est violée, on définit l'erreur
            $context
                ->buildViolation("Il faut être agé de 8 ans minimum pour participer à une course")// message
                ->atPath('ageMax')// attribut de l'objet qui est violé
                ->addViolation() // ceci déclenche l'erreur, ne l'oubliez pas
            ;
        }

        if ($this->getAgeMin() < $this->ageMax) {
            $context
                ->buildViolation("L'age maximum doit être suppérieur à l'age minimum")// message
                ->atPath('ageMax')// attribut de l'objet qui est violé
                ->addViolation() // ceci déclenche l'erreur, ne l'oubliez pas
            ;
        }

    }

}
