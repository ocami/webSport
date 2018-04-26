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
     * Return array of Competitions if are valid, foreach set Competition.competitorCanRegister if current user is competitor
     *
     *@return \ArrayObject
     */
    public function showAll()
    {
        $competitions = $this->em->getRepository(Competition::class)->allValidByDate();

        if ($this->ac->isGranted('ROLE_COMPETITOR')){
            $competitions['competitionsNoPassed'] = $this->competitorCanRegisterList(
                $competitions['competitionsNoPassed'],
                $this->us->currentUserApp(Competitor::class)
            );
        }

        return $competitions;
    }

    /**
     * Same showAll() with geoJson data
     *
     *@return array
     */
    public function mapData()
    {
        $pastCompetitions = array();
        $futureCompetitions = array();

        $competitions = $this->showAll();

        foreach ($competitions['competitionsNoPassed'] as $competition) {
            array_push($futureCompetitions, $this->popUpString($competition));
        }

        foreach ($competitions['competitionsPassed'] as $competition) {
            array_push($pastCompetitions, $this->popUpString($competition));
        }

        return array(
            "pastCompetitions" => $pastCompetitions,
            "futureCompetitions" => $futureCompetitions,
        );
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
        $competitor = $this->us->setCategoryCompetitor($competitor);

        if ($competition->getCategories()->contains($competitor->getCategory()))
            $competition->setCompetitorCanRegister(true);

        return $competition;
    }

    /**
     * Call function competitorCanRegister() foreach competition of array
     *
     * @param array of competitions
     * @param Competitor $competitor
     *
     * @return array of competitions
     */
    private function competitorCanRegisterList($competitions, Competitor $competitor)
    {
        for ($i = 0; $i < count($competitions); $i++) {
            $competitions[$i] = $this->competitorCanRegister($competitions[$i], $competitor);
        }

        return $competitions;
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
            $description = $description . "<img class='leaflet-popup-img' src='..\img\cup.jpg'/>";

        if ($competition->getCompetitorCanRegister())
            $description = $description . "<img class='leaflet-popup-img' src='..\img\canRegister.png'/>";

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

}