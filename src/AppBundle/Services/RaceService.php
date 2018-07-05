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
    private $compS;


    public function __construct(
        EntityManagerInterface $em,
        UserService $us,
        TokenStorageInterface $ts,
        AuthorizationChecker $ac,
        CodeService $cs,
        CompetitionService $compS)
    {
        $this->em = $em;
        $this->us = $us;
        $this->cs = $cs;
        $this->ts = $ts;
        $this->ac = $ac;
        $this->compS = $compS;
    }


    public function create(Race $race)
    {
        $categoriesId = json_decode($race->getCategoriesString());
        $competition = $race->getCompetition();

        foreach ($categoriesId as $category) {
            $category = $this->em->getRepository(Category::class)->find($category);

            $race->addCategory($category);

            if (!$competition->getCategories()->contains($category))
                $competition->addCategory($category);
        }

        $race =  $this->cs->generateCode($race);

        if(!$race->getName())
            $race->setName($race->getCode());

        $this->em->persist($race);
        $this->em->persist($competition);
        $this->em->flush();
    }

    public function update(Race $race)
    {
        $categoriesId = json_decode($race->getCategoriesString());
        $competition = $race->getCompetition();
        $oldCategories = $race->getCategories();

        foreach ($oldCategories as $category) {
            if ((!in_array($category->getId(),$categoriesId)))
                $race->removeCategory($category);
        }

        foreach ($categoriesId as $categoryId) {
            $category = $this->em->getRepository(Category::class)->find($categoryId);

            if (!$race->getCategories()->contains($category))
                $race->addCategory($category);
        }

        $this->compS->categoriesUpdate($competition);

        $this->em->persist($race);
        $this->em->persist($competition);
        $this->em->flush();
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

        if ($race->getCategories()->contains($competitor->getCategory()))
            if ($race->getEnrol())
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