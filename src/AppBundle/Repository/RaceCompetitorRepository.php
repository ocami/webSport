<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Category;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCompetitor;


class RaceCompetitorRepository extends EntityRepository
{
    /**
     * RaceCompetitor collection order by chrono
     * @param Race $race
     * @return array
     */
    public function allByRace(Race $race)
    {
        $rc = $this->createQueryBuilder('rc')
            ->where('rc.race = :race')
            ->setParameter('race', $race)
            ->orderBy('rc.chrono', 'asc')
            ->getQuery()->getResult();

        return $rc;
    }

    /**
     * RaceCompetitor collection order by competitor.lastName
     * @param Race $race
     * @return array
     */
    public function allOrderByCompetitorLastName(Race $race)
    {
        $rc = $this->createQueryBuilder('rc')
            ->innerJoin('rc.competitor', 'c')
            ->where('rc.race = :race')
            ->setParameter('race', $race)
            ->orderBy('c.lastName');

        $rc = $rc->getQuery()->getResult();
        return $rc;
    }

    /**
     * array strings [number,rank,rankCategory,chronoString,points,competitor[id,code,firstName,lastName,date,gender]]
     * order by rank
     * @param  Race $race
     * @return array
     */
    public function allByRaceToString(Race $race)
    {
        $rc = $this->createQueryBuilder('rc')
            ->innerJoin('rc.competitor', 'c')
            ->select('
            rc.number, 
            rc.rank, 
            rc.rankCategory, 
            rc.chronoString, 
            rc.points, 
            c.id,
            c.code, 
            c.firstName, 
            c.lastName, 
            c.date, 
            c.gender')
            ->where('rc.race = :race')
            ->setParameter('race', $race)
            ->orderBy('rc.rank')
            ->getQuery()->getResult();

        return $rc;
    }

    /**
     * array strings [number,rank,rankCategory,chronoString,points,competitor[id,code,firstName,lastName,date,gender]]
     * order by chrono
     * @param  Race $race
     * @param  Category $category
     * @return array
     */
    public function allByRaceCategoryToString(Race $race,$category)
    {
        $rc = $this->createQueryBuilder('rc')
            ->innerJoin('rc.competitor', 'c')
            ->select('
            rc.number, 
            rc.rank, 
            rc.rankCategory, 
            rc.chronoString, 
            rc.points, 
            c.id,
            c.code, 
            c.firstName, 
            c.lastName, 
            c.date, 
            c.gender')
            ->where('rc.race = :race')
            ->andWhere('c.date >= :dateMax AND c.date <= :dateMin')
            ->setParameter('race', $race->getId())
            ->setParameter('dateMax', new \DateTime("01-01-" . $category->getAgeMax()))
            ->setParameter('dateMin', new \DateTime("31-12-" . $category->getAgeMin()))
            ->orderBy('rc.chrono');

        if ($category->getGender() != 'mx') {
            $rc = $rc->andWhere('c.gender = :gender')
                ->setParameter('gender', $category->getGender());
        }

        $rc = $rc->getQuery()->getResult();
        return $rc;
    }

    /**
     * RaceCompetitor collection order by chrono
     * @param  Race $race
     * @param  Category $category
     * @return array
     */
    public function allByRaceCategory(Race $race, Category $category)
    {
        $rc = $this->createQueryBuilder('rc')
            ->innerJoin('rc.competitor', 'c')
            ->where('rc.race = :race')
            ->andWhere('c.date > :dateMax AND c.date < :dateMin')
            ->setParameter('race', $race->getId())
            ->setParameter('dateMax', new \DateTime("01-01-" . $category->getAgeMax()))
            ->setParameter('dateMin', new \DateTime("31-12-" . $category->getAgeMin()))
            ->orderBy('rc.chrono');

        if ($category->getGender() != 'mx') {
            $rc = $rc->andWhere('c.gender = :gender')
                ->setParameter('gender', $category->getGender());
        }

        $rc = $rc->getQuery()->getResult();
        return $rc;
    }

    /**
     * RaceCompetitor if $competitor is enrol for race | null
     * @param Race $race
     * @param Competitor $competitor
     * @return RaceCompetitor
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onceByRaceCompetitor(Race $race, Competitor $competitor)
    {
        $rc = $this->createQueryBuilder('rc')
            ->where('rc.race = :race')
            ->andWhere('rc.competitor = :competitor')
            ->setParameter('race', $race->getId())
            ->setParameter('competitor', $competitor->getId())
            ->getQuery()->getOneOrNullResult();

        return $rc;
    }

    /**
     * true if $competitor is enrol for race
     * @param Race
     * @param Competitor
     * @return boolean
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function competitorIsRegisterToRace(Race $race,Competitor $competitor)
    {
        if(is_null($this->onceByRaceCompetitor($race, $competitor)))
            return false;

        return true;
    }
}
