<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adress
 *
 * @ORM\Table(name="villes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AddressRepository")
 */
class Address
{
    /**
     * @var int
     *
     * @ORM\Column(name="ville_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_nom", type="string", length=45, nullable=false)
     */
    private $villeNom;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_slug", type="string", length=255, nullable=false)
     */
    private $villeSlug;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_nom_reel", type="string", length=45, nullable=false)
     */
    private $villeNomReel;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_departement", type="string", length=3, nullable=false)
     */
    private $villeDepartement;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_code_commune", type="string", length=5, nullable=false)
     */
    private $villeCodeCommune;



    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get villeNom
     *
     * @return string
     */
    public function getVilleNom()
    {
        return $this->villeNom;
    }

    /**
 * Get villeSlug
 *
 * @return string
 */
    public function getVilleSlug()
    {
        return $this->villeSlug;
    }

    /**
     * Get villeNomReel
     *
     * @return string
     */
    public function getvilleNomReel()
    {
        return $this->villeNomReel;
    }

    /**
     * Get villeDepartement
     *
     * @return string
     */
    public function getVilleDepartement()
    {
        return $this->villeDepartement;
    }

    /**
     * Get villeDepartement
     *
     * @return string
     */
    public function getVilleCodeCommune()
    {
        return $this->villeCodeCommune;
    }

}

