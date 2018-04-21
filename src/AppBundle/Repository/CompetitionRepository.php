<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Competition;
use AppBundle\Entity\Organizer;

class CompetitionRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @return array object
     */
    public function byDate()
    {
        $c = $this->createQueryBuilder('c')
            ->orderBy('c.dateStart');

        $competitionsPassed = $c
            ->Where('c.dateEnd > :today')
            ->setParameter('today', strtotime("now"))
            ->getQuery()->getResult();

        $competitionsNoPassed = $c
            ->Where('c.dateEnd < :today')
            ->setParameter('today', strtotime("now"))
            ->getQuery()->getResult();


        $competitions = array('competitionsPassed' => $competitionsPassed, 'competitionsNoPassed' => $competitionsNoPassed);

        return $competitions;
    }

    /**
     * @return array object
     */
    public function byOrganizer(Organizer $organizer)
    {
        $c = $this->createQueryBuilder('c');

        $competitionsPassed = $c
            ->where('c.organizer = :organizer')
            ->andWhere('c.dateEnd > :today')
            ->setParameter('organizer', $organizer)
            ->setParameter('today', strtotime("now"))
            ->orderBy('c.dateStart', 'DESC')
            ->getQuery()->getResult();

        $competitionsNoPassed = $c
            ->where('c.organizer = :organizer')
            ->andWhere('c.dateEnd < :today')
            ->setParameter('organizer', $organizer)
            ->setParameter('today', strtotime("now"))
            ->orderBy('c.dateStart', 'ASC')
            ->getQuery()->getResult();


        $competitions = array('competitionsPassed' => $competitionsPassed, 'competitionsNoPassed' => $competitionsNoPassed);

        return $competitions;
    }


    public function isValid(Competition $competition){
        $nb = $this->createQueryBuilder('c')
            ->select('count(c)')
            ->innerJoin('c.races','r')
            ->where('c.id = :id')
            ->andWhere('r.valid = :bool')
            ->setParameter('id', $competition)
            ->setParameter('bool', 1)
            ->getQuery()->getSingleScalarResult();

        $nb =  intval($nb);

        if (intval($nb) > 0)
            return true;

        return false;
    }

    public function isInChampionship(Competition $competition){
        $nb = $this->createQueryBuilder('c')
            ->select('count(c)')
            ->innerJoin('c.races','r')
            ->where('c.id = :id')
            ->andWhere('r.inChampionship = :bool')
            ->setParameter('id', $competition)
            ->setParameter('bool', 1)
            ->getQuery()->getSingleScalarResult();

        $nb =  intval($nb);

        if (intval($nb) > 0)
            return true;

        return false;
    }
}
