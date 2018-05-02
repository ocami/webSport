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
use AppBundle\Entity\RaceCompetitor;
use AppBundle\Entity\Category;
use AppBundle\Entity\Race;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class RaceService
{
    private $em;
    private $us;
    private $cs;
    private $ts;
    private $ac;


    public function __construct(
        EntityManagerInterface $em,
        UserService $us,
        TokenStorageInterface $ts,
        AuthorizationChecker $ac,
        CodeService $cs)
    {
        $this->em = $em;
        $this->us = $us;
        $this->cs = $cs;
        $this->ts = $ts;
        $this->ac = $ac;

    }


    public function create(Race $race)
    {
        $categoriesId = json_decode($race->getCategoriesString());
        $competition = $race->getCompetition();

        foreach ($categoriesId as $category) {
            $category = $this->em->getRepository(Category::class)->find($category);

            if (!$race->getCategories()->contains($category))
                $race->addCategory($category);

            if (!$competition->getCategories()->contains($category))
                $competition->addCategory($category);
        }

        $this->em->persist($race);
        $this->em->persist($competition);
        $this->em->flush();
        $this->cs->generateCode($race);
    }

    public function toString(Race $race)
    {
        $raceToString = [];

        $raceToString['race'] = [];
        $raceToString['competition'] = [];

        $raceToString['race']['name'] = $race->getName();
        $raceToString['race']['distance'] = $race->getDistance();
        $raceToString['race']['inChampionship'] = $race->getInChampionship();
        $raceToString['race']['date'] = $race->getDateTime();

        $raceToString['competition']['name'] = $race->getCompetition()->getName();
        $raceToString['competition']['organizer'] = $race->getCompetition()->getOrganizer()->getName();

        $raceToString['location'] = $race->getCompetition()->getLocation();

        return $raceToString;
    }

    public function postSelectAll($races)
    {
        foreach ($races as $race) {
            $this->postSelectOne($race);
        }

        return $races;
    }

    public function postSelectOne(Race $race)
    {
            $nbC = $this->em->getRepository(Category::class)->count();

            if (count($race->getCategories()) == $nbC)
                $race->setFullCat(true);

        return $race;
    }



    public function CompetitorRegisterStatus(Race $race, $competitor)
    {
        if ($this->em->getRepository(RaceCompetitor::class)->competitorIsRegisterToRace($race, $competitor))
            return 2;

        if ($race->getCategories()->contains($this->us->getCategoryCompetitor($competitor)))
            return 1;

        return 0;
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