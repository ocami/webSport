<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 14/01/2018
 * Time: 08:21
 */

namespace AppBundle\Services;

use AppBundle\Entity\Competitor;
use AppBundle\Entity\Competition;
use AppBundle\Entity\Category;
use AppBundle\Entity\Race;
use Doctrine\ORM\EntityManagerInterface;


class RaceService
{
    private $em;
    private $us;
    private $cs;

    public function __construct(EntityManagerInterface $em, UserService $us, CodeService $cs)
    {
        $this->em = $em;
        $this->us = $us;
        $this->cs = $cs;
    }

    public function toString(Race $race)
    {
        $raceToString = [];

        $raceToString['race'] = [];
        $raceToString['competition'] = [];

        $raceToString['race']['name'] = $race->getName();
        $raceToString['race']['distance'] = $race->getDistance();
        $raceToString['race']['inChampionship'] = $race->getInChampionship();
        $raceToString['race']['date'] = $race->getDateString();

        $raceToString['competition']['name'] = $race->getCompetition()->getName();
        $raceToString['competition']['organizer'] = $race->getCompetition()->getOrganizer()->getName();

        $raceToString['location'] = $race->getCompetition()->getLocation();

        return $raceToString;
    }

    public function competitorCanEntry($race)
    {
        $competitor = $this->us->currentUserApp(Competitor::class);
        $competitorYear = $competitor->getDate()->format('Y');

        $race->setCompetitorCanEntry(false);

        foreach ($race->getCategories() as $category) {
            if ($competitorYear <= $category->getAgeMin() AND $competitorYear >= $category->getAgeMax()) {

                if ($competitorYear < $category->getAgeMin() AND $competitorYear > $category->getAgeMax()) {
                    $race->setCompetitorCanEntry(true);
                    return $race;
                }

            }
        }
        return $race;
    }

    public function racesCompetitorCanEntry($races)
    {
        foreach ($races as $race) {
            $race = $this->competitorCanEntry($race);
        }
        return $races;
    }

    public function competitorCanRegister($race)
    {
        $competitor = $this->us->currentUserApp(Competitor::class);
        $competitorYear = $competitor->getDate()->format('Y');

        $race->setCompetitorCanEntry(false);

        foreach ($race->getCategories() as $category) {
            if ($competitorYear <= $category->getAgeMin() AND $competitorYear >= $category->getAgeMax()) {

                if ($competitorYear < $category->getAgeMin() AND $competitorYear > $category->getAgeMax()) {
                    $race->setCompetitorCanEntry(true);
                    return $race;
                }

            }
        }
        return $race;
    }

    public function competitorIsRegister($race){

    }

    public function create(Race $race)
    {
        $categoriesId = json_decode($race->getCategoriesString());
        $competition = $race->getCompetition();

        $i = 0;
        foreach ($categoriesId as $category) {
            $i++;
            $category = $this->em->getRepository(Category::class)->find($category);

            $race->addCategory($category);

            if (!$competition->getCategories()->contains($category))
                $competition->addCategory($category);
        }

        $nbC = $this->em->getRepository(Category::class)->count();

        if ($nbC == $i)
            $race->setFullCat(true);

        $this->em->persist($race);
        $this->em->persist($competition);
        $this->em->flush();
        $this->cs->generateCode($race);
    }

    public function adminSuperviseUpdate($data)
    {
        $race = $this->em->getRepository(Race::class)->find($data['race']);

        $race->setSupervised(true);
        $race->setValid($data['valid']);
        $race->setInChampionship($data['inChampionship']);

        if ($data['inChampionship'])
            $race->setRequestInChampionship(false);

        $this->em->persist($race);
        $this->em->flush($race);

        //Competition update
        $cr = $this->em->getRepository(Competition::class);
        $competition = $race->getCompetition();

        $competition->setValid($cr->isValid($competition));
        $competition->setInChampionship($cr->isInChampionship($competition));

        $this->em->persist($competition);
        $this->em->flush($competition);
    }

}