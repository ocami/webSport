<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Competition;
use AppBundle\Entity\Organizer;

class CompetitionRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @return array object
     */
    public function allValidByDate()
    {
        $parameters = array(
            'isValid' => 1,
            'today' => date('Y-m-d')
        );

        $competitionsNoPassed = $this->createQueryBuilder('c')
            ->orderBy('c.dateStart')
            ->Where('c.valid = :isValid')
            ->andWhere('c.dateEnd > :today')
            ->setParameters($parameters)
            ->getQuery()->getResult();

        $competitionsPassed = $this->createQueryBuilder('c')
            ->orderBy('c.dateStart')
            ->Where('c.valid = :isValid')
            ->andWhere('c.dateEnd < :today')
            ->setParameters($parameters)
            ->getQuery()->getResult();

        $competitions = array('competitionsPassed' => $competitionsNoPassed, 'competitionsNoPassed' => $competitionsPassed);

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
