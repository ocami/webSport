<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCompetitor;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * RaceCompetitorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RaceCompetitorRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return array string
     */
    public function rcOrderByRanckToString($race)
    {
        $rc = $this->createQueryBuilder('rc')
            ->innerJoin('rc.competitor', 'c')
            ->select('c.code, rc.number, rc.ranck, rc.chronoString, c.firstName, c.lastName')
            ->where('rc.race = :race')
            ->setParameter('race', $race)
            ->orderBy('rc.ranck')
            ->getQuery()->getResult();

        return $rc;
    }

    /**
     * @return array object
     */
    public function rcOrderByChrono($race)
    {
        $rc = $this->createQueryBuilder('rc')
            ->where('rc.race = :race')
            ->setParameter('race', $race)
            ->orderBy('rc.chrono', 'asc')
            ->getQuery()->getResult();

        return $rc;
    }

    /**
     * @return array string
     */
    public function categoriesRanckToString(Category $category, Race $race)
    {
        $rc = $this->createQueryBuilder('rc')
            ->innerJoin('rc.competitor', 'c')
            ->select('c.id, c.code, rc.number, rc.ranck, rc.chronoString, c.firstName, c.lastName')
            ->where('rc.race = :race')
            ->andWhere('c.date > :dateMax AND c.date < :dateMin')
            ->setParameter('race', $race->getId())
            ->setParameter('dateMax', new \DateTime("01-01-" . $category->getAgeMax()))
            ->setParameter('dateMin', new \DateTime("31-12-" . $category->getAgeMin()))
            ->orderBy('rc.chrono');

        if ($category->getSexe() != 'mx') {
            $rc = $rc->andWhere('c.sexe = :sexe')
                ->setParameter('sexe', $category->getSexe());
        }

        $rc = $rc->getQuery()->getResult();
        return $rc;
    }

    /**
     * @return array object
     */
    public function categoriesRanck(Category $category, Race $race)
    {
        $rc = $this->createQueryBuilder('rc')
            ->innerJoin('rc.competitor', 'c')
            ->select('rc')
            ->where('rc.race = :race')
            ->andWhere('c.date > :dateMax AND c.date < :dateMin')
            ->setParameter('race', $race->getId())
            ->setParameter('dateMax', new \DateTime("01-01-" . $category->getAgeMax()))
            ->setParameter('dateMin', new \DateTime("31-12-" . $category->getAgeMin()))
            ->orderBy('rc.chrono');

        if ($category->getSexe() != 'mx') {
            $rc = $rc->andWhere('c.sexe = :sexe')
                ->setParameter('sexe', $category->getSexe());
        }

        $rc = $rc->getQuery()->getResult();
        return $rc;
    }

    /**
     * @return boolean
     */
    public function competitorIsRegisterToRace(Race $race, Competitor $competitor)
    {
        if(is_null($this->getRC($race, $competitor)))
            return false;

        return true;
    }

    /**
     * @return object
     */
    public function getRC(Race $race, Competitor $competitor)
    {
        $rc = $this->createQueryBuilder('rc')
            ->where('rc.race = :race')
            ->andWhere('rc.competitor = :competitor')
            ->setParameter('race', $race->getId())
            ->setParameter('competitor', $competitor->getId())
            ->getQuery()->getResult();

        if (!count($rc))
            return null;

        return $rc[0];
    }

    /**
     * @return array object
     */
    public function competitorsEnrolByLastName($race)
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
     * @return array string
     */
    public function byCompetitor(Competitor $competitor)
    {

        $rc = $this->createQueryBuilder('rc')
            ->innerJoin('rc.race', 'r')
            ->innerJoin('r.competition', 'compet')
            ->select('r.id, r.passed, r.name, r.date, rc.ranck, rc.chronoString, compet.ville')
            ->where('rc.competitor = :idCompetitor')
            ->setParameter('idCompetitor', $competitor->getId());

        $racesPassed = $rc
            ->andWhere('r.passed=:bool')
            ->setParameter('bool', true)
            ->orderBy('r.date', 'DESC')
            ->getQuery()->getResult();

        $racesNoPassed = $rc
            ->andWhere('r.passed=:bool')
            ->setParameter('bool', false)
            ->orderBy('r.date', 'ASC')
            ->getQuery()->getResult();


        $races = array('racePassed' => $racesPassed, 'raceNoPassed' => $racesNoPassed);

        return $races;
    }

}
