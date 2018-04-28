<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Competition;
use AppBundle\Entity\Organizer;
use phpDocumentor\Reflection\Types\Integer;

class CompetitionRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return array of competitions separed in passed and not passed
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

        $competitions = array('competitionsPassed' => $competitionsPassed, 'competitionsNoPassed' => $competitionsNoPassed);

        return $competitions;
    }

    /**
     * @return array of competitions separed in passed and not passed
     *
     * @param int $organizerId
     *
     */
    public function byOrganizer($organizerId)
    {
        $parameters = array(
            'organizer' => $organizerId,
            'today' => date('Y-m-d')
        );

        $competitionsNoPassed = $this->createQueryBuilder('c')
            ->orderBy('c.dateStart')
            ->Where('c.organizer = :organizer')
            ->andWhere('c.dateEnd > :today')
            ->setParameters($parameters)
            ->getQuery()->getResult();

        $competitionsPassed = $this->createQueryBuilder('c')
            ->orderBy('c.dateStart')
            ->Where('c.organizer = :organizer')
            ->andWhere('c.dateEnd < :today')
            ->setParameters($parameters)
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
