<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 14/01/2018
 * Time: 08:21
 */

namespace AppBundle\Services;

use AppBundle\Entity\Competition;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Category;
use AppBundle\Entity\Race;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Location;
use AppBundle\Entity\Organizer;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;


class CompetitionService
{
    private $em;
    private $router;
    private $us;
    private $ds;
    private $ac;

    public function __construct(
        EntityManagerInterface $em,
        RouterInterface $router,
        UserService $us,
        AuthorizationChecker $ac,
        DateService $ds
    )
    {
        $this->em = $em;
        $this->router = $router;
        $this->us = $us;
        $this->ac = $ac;
        $this->ds = $ds;
    }


    /**
     * Set competitions attributes where depend on other class
     *
     * @param array
     *
     * @return array
     */
    public function postSelect($competitions)
    {
        $nbC = $this->em->getRepository(Category::class)->count();
        $culledCompetitions = array(
            'passed' => [],
            'future' => []
        );

        foreach ($competitions as $competition) {

            if (count($competition->getCategories()) == $nbC)
                $competition->setFullCat(true);

            if($competition->getIsPassed())
                array_push($culledCompetitions['passed'],$competition);
            else
                array_push($culledCompetitions['future'],$competition);
        }

        return $culledCompetitions;
    }

    /**
     * Create new Competition and new location if this location don't exist, set organizer, set location
     *
     * use Competition.locationString attribute to create Location object
     * set Competition.location
     *
     * @param Competition $competition
     * @param Organizer $organizer
     *
     */
    public function create(Competition $competition, Organizer $organizer)
    {

        $locationData = json_decode($competition->getLocationString(), true);

        $location = $this->em->getRepository(Location::class)->findOneByDataId($locationData['id']);

        if ($location == null) {
            $location = new Location();
            $location->setDataId($locationData['id']);
            $location->setStreet($locationData['street']);
            $location->setPostCode($locationData['postCode']);
            $location->setCity($locationData['city']);
            $location->setX($locationData['x']);
            $location->setY($locationData['y']);
        }

        $competition->setLocation($location);
        $competition->setOrganizer($organizer);

        $em = $this->em;
        $em->persist($location);
        $em->persist($organizer);
        $em->persist($competition);
        $em->flush();
    }

    /**
     *
     * @return array
     */
    public function mapData()
    {
        $pastCompetitions = array();
        $futureCompetitions = array();

        $competitions = $this->em->getRepository(Competition::class)->allValid();
        $competitions = $this->us->addUserDataInCompetitions($competitions);

        foreach ($competitions as $competition) {

            if ($competition->getIsPassed())
                array_push($pastCompetitions, $this->popUpString($competition));
            else
                array_push($futureCompetitions, $this->popUpString($competition));
        }

        return array(
            "pastCompetitions" => $pastCompetitions,
            "futureCompetitions" => $futureCompetitions,
        );
    }

    /**
     * Return array data location and description of competition in geoJson format
     *
     * @param Competition $competition
     *
     * @return array
     */
    private function popUpString(Competition $competition)
    {
        $description =
            "<b>" . $competition->getName() . "</b><br>Du "
            . $this->ds->format($competition->getDateStart(), 'd-m') . " au "
            . $this->ds->format($competition->getDateEnd(), 'd-m') .
            "<br> <a href='" .
            $this->router->generate('competition_show', array('id' => $competition->getId())) .
            "'>Voir cette compétition</a><br>";

        if ($competition->getInChampionship())
            $description = $description . "<img class='leaflet-popup-img' src='/webSport/web/img/cup.jpg'/>";

        if ($competition->getCompetitorRegister() == 1)
            $description = $description . "<img class='leaflet-popup-img' src='/webSport/web/img/canRegister.png'/>";

        if ($competition->getCompetitorRegister() == 2)
            $description = $description . "<img class='leaflet-popup-img' src='/webSport/web/img/race_start.png'/>";

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

    /**
     * Check if competition contains race for which competitor can register
     *
     * @param Competition $competition
     * @param Competitor $competitor
     *
     * @return Competition
     */

    public function competitorCanRegister(Competition $competition, Competitor $competitor)
    {
        if ($competition->getCategories()->contains($competitor->getCategory()))
            $competition->setCompetitorCanRegister(true);

        return $competition;
    }

    public function categoriesUpdate(Competition $competition)
    {

        $races = $competition->getRaces();
        $categoriesId = [];
        $oldCategories = $competition->getCategories();
        $oldCategoriesID = [];

        foreach ($races as $race) {
            foreach ($race->getCategories() as $category) {
                if ((!in_array($category->getId(), $categoriesId)))
                    array_push($categoriesId, $category->getId());
            }
        }

        foreach ($oldCategories as $category) {
            if ((!in_array($category->getId(), $categoriesId)))
                $competition->removeCategory($category);

            array_push($oldCategoriesID, $category->getId());
        }

        foreach ($categoriesId as $categoryId) {
            if ((!in_array($categoryId, $oldCategoriesID)))
                $competition->addCategory($this->em->getRepository(Category::class)->find($categoryId));
        }


        return $competition;
    }

}