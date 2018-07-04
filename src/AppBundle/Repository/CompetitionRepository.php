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
        $competitions = $this->createQueryBuilder('c')
            ->orderBy('c.dateStart')
            ->Where('c.valid = :isValid')
            ->setParameter('isValid', true)
            ->getQuery()->getResult();

        return $competitions;
    }

    /**
     * @return array of competitions separed in passed and not passed
     */
    public function allValidFirstFive()
    {
        $date = date('Y-m-d', strtotime('now'));

        $competitions = $this->createQueryBuilder('c')
            ->orderBy('c.dateStart')
            ->Where('c.valid = :isValid')
            ->andWhere('c.dateStart >= :date')
            ->setParameter('isValid', true)
            ->setParameter('date', $date)
            ->setFirstResult(0)
            ->setMaxResults(5)
            ->getQuery()->getResult();

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
        $competitions = $this->createQueryBuilder('c')
            ->orderBy('c.dateStart')
            ->Where('c.organizer = :organizer')
            ->setParameter('organizer', $organizerId)
            ->getQuery()->getResult();

        return $competitions;
    }

    public function countNotSupervisedRace(Competition $competition)
    {
        $rawSql = "SELECT COUNT(r.id) FROM competition c
                   INNER JOIN race r ON  c.id = r.competition_id
                   WHERE c.id = ".$competition->getId()."
                   AND r.supervised = 0";

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute([]);

        return $stmt->fetchColumn();
    }

    public function isValid(Competition $competition)
    {
        $nb = $this->createQueryBuilder('c')
            ->select('count(c)')
            ->innerJoin('c.races', 'r')
            ->where('c.id = :id')
            ->andWhere('r.valid = :bool')
            ->setParameter('id', $competition)
            ->setParameter('bool', 1)
            ->getQuery()->getSingleScalarResult();

        $nb = intval($nb);

        if (intval($nb) > 0)
            return true;

        return false;
    }

    public function isInChampionship(Competition $competition)
    {
        $nb = $this->createQueryBuilder('c')
            ->select('count(c)')
            ->innerJoin('c.races', 'r')
            ->where('c.id = :id')
            ->andWhere('r.inChampionship = :bool')
            ->setParameter('id', $competition)
            ->setParameter('bool', 1)
            ->getQuery()->getSingleScalarResult();

        $nb = intval($nb);

        if (intval($nb) > 0)
            return true;

        return false;
    }
}
