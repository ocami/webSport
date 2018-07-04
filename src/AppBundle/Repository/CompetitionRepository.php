<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Competition;
use AppBundle\Entity\Organizer;

class CompetitionRepository extends EntityRepository
{
    /**
     * Competitions collection order by date
     * @return array
     */
    public function allValid()
    {
        $competitions = $this->createQueryBuilder('c')
            ->Where('c.valid = :isValid')
            ->setParameter('isValid', true)
            ->orderBy('c.dateStart')
            ->getQuery()->getResult();

        return $competitions;
    }

    /**
     * collection of five most recent Competitions order by date
     * @return array
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
     * Competitions collection
     * @param Organizer
     * @return array
     */
    public function allByOrganizer($organizer)
    {
        $competitions = $this->createQueryBuilder('c')
            ->orderBy('c.dateStart')
            ->Where('c.organizer = :organizer')
            ->setParameter('organizer', $organizer)
            ->getQuery()->getResult();

        return $competitions;
    }

    /**
     *
     * @param Competition
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function countNotSupervisedRace($competition)
    {
        $rawSql = "SELECT COUNT(r.id) FROM competition c
                   INNER JOIN race r ON  c.id = r.competition_id
                   WHERE c.id = ".$competition->getId()."
                   AND r.supervised = 0";

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute([]);

        return $stmt->fetchColumn();
    }

    /**
     * true if competition contain at least one valid race
     * @param Competition
     * @return boolean
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isValid($competition)
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

    /**
     * true if competition contain at least one inChampionship race
     * @param Competition
     * @return boolean
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isInChampionship($competition)
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
