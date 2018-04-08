<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 10/01/2018
 * Time: 20:18
 */

namespace AppBundle\Services;

use AppBundle\Entity\Category;
use AppBundle\Entity\Championship;
use AppBundle\Entity\Competition;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Organizer;
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCompetitor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use AppBundle\Services\UserService;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;


class GeoJsonService
{

    private $em;
    private $router;
    private $us;

    public function __construct(
        EntityManagerInterface $em,
        RouterInterface $router,
        UserService $us,
        AuthorizationChecker $ac)
    {
        $this->em = $em;
        $this->router = $router;
        $this->us = $us;
        $this->ac = $ac;
    }

    public function competiton(Competition $competition)
    {
        $description =
            "<b>" . $competition->getName() . "</b><br>Du "
            . $competition->getDateStart()->format('d-m') . " au "
            . $competition->getDateEnd()->format('d-m') .
            "<br> <a href='" .
            $this->router->generate('competition_show', array('id' => $competition->getId())) .
            "'>Voir cette compétition</a><br>
            <img class='leaflet-popup-img' src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRXi35igLJx_E5h8IZ032FB1XSldp8d6h4iVUm9BXWI8Mi7O17Hcg'/>
            
";
        if ($this->ac->isGranted('ROLE_COMPETITOR')){
            $competitor = $this->us->getCategoryCompetitor();

            if ($competition->getCategories()->contains($competitor->getCategory()))
                $description = $description . "<img class='leaflet-popup-img' src='http://fotomelia.com/wp-content/uploads/edd/2015/05/main-pouce-lev%C3%A9-vers-le-haut-like-liker-lik%C3%A9-clipart-images-gratuites-libres-de-droits-1560x1723.png'/>";
        }


        $properties = array(
            'name' => $competition->getName(),
            'description' => $description,
        );

        $geometry = array(
            "type" => "Point",
            "coordinates" => array($competition->getLocation()->getY(), $competition->getLocation()->getX())
        );

        $feature = array(
            "type" => "Feature",
            "properties" => $properties,
            "geometry" => $geometry,
        );

        return $feature;
    }

    public function competitorCanRegister($competition)
    {
        $properties = array(
            'name' => 'truc',
        );

        $geometry = array(
            "type" => "Point",
            "coordinates" => array($competition->getLocation()->getY(), $competition->getLocation()->getX())
        );

        $feature = array(
            "type" => "Feature",
            "properties" => $properties,
            "geometry" => $geometry,
        );

        return $feature;
    }

    public function competitions()
    {
        $pastCompetitions = array();
        $futureCompetitions = array();
        $containsCompetitorCategoryCompetitions = array();

        $competitions = $this->em->getRepository(Competition::class)->findAll();

        foreach ($competitions as $competition) {

            if ($competition->getDateEnd() > new \DateTime()) {
                array_push($pastCompetitions, $this->competiton($competition));
            } else {
                array_push($futureCompetitions, $this->competiton($competition));
                //array_push($containsCompetitorCategoryCompetitions, $this->competitorCanRegister($competition));
            }
        }

        return array(
            "pastCompetitions" => $pastCompetitions,
            "futureCompetitions" => $futureCompetitions,
            "competitorCanRegister" => $futureCompetitions
        );
    }
}