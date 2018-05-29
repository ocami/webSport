<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Competition;
use AppBundle\Entity\Race;


/**
 * RaceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RaceRepository extends \Doctrine\ORM\EntityRepository
{

    public function allValidByDate()
    {
        $races = $this->createQueryBuilder('r')
            ->orderBy('r.dateTime')
            ->Where('r.valid = :isValid')
            ->setParameter('isValid', true)
            ->getQuery()->getResult();

        return $races;
    }

    public function allValidByCompetition($competition)
    {
        $races = $this->createQueryBuilder('r')
            ->orderBy('r.dateTime')
            ->Where('r.valid = :isValid')
            ->andWhere('r.competition = :competition')
            ->setParameter('isValid', true)
            ->setParameter('competition', $competition)
            ->getQuery()->getResult();

        return $races;
    }

    /**
     * @param Race $race
     * @return Race|array
     * contains array race and array categories
     *
     */
    public function toString(Race $race)
    {
        $race = $this->createQueryBuilder('r')
            ->innerJoin('r.competition', 'c')
            ->innerJoin('c.organizer', 'o')
            ->innerJoin('c.location', 'l')
            ->select('r.id, r.name, r.distance, r.inChampionship, r.dateTime, 
                            c.id as competitionId, c.name as competitionName, 
                            o.id as organizerId, o.name as organizerName,
                            l.street, l.postCode, l.city, l.x, l.y
                            ')
            ->where('r.id = :id')
            ->setParameter('id', $race)
            ->getQuery()->getResult();

        $categories = $this->createQueryBuilder('r')
            ->leftJoin('r.categories', 'c')
            ->select('c.id, c.name')
            ->where('r.id = :id')
            ->setParameter('id', $race)
            ->getQuery()->getResult();

        $race['race'] = $race[0];
        unset($race[0]);

        $race['categories'] = $categories;

        return $race;
    }


    public function lastId($class)
    {
        return $this->createQueryBuilder('a')
            ->select('MAX(e.id)')
            ->from('AppBundle:' . $class, 'e')
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function countNotSupervisedRaces()
    {
        $nb = $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->where('r.supervised = :supervised')
            ->setParameter('supervised', 0)
            ->getQuery()
            ->getSingleScalarResult();

        return $nb;
    }

    Public function search($data)
    {
        //categories
        $whereCategories = "AND (rc.category_id = " . $data['categories'][0];
        for ($i = 0; $i < count($data['categories']); $i++) {
            if ($i >= 1)
                $whereCategories .= " OR rc.category_id = ".$data['categories'][$i];
        }
        $whereCategories .= ")";

        //dep
        $wherePostCode = "AND (l.postCode LIKE '" . $data['dep'][0]."%'";
        for ($i = 0; $i < count($data['dep']); $i++) {
            if ($i >= 1)
                $wherePostCode .= " OR l.postCode LIKE '".$data['dep'][$i]."%'";
        }
        $wherePostCode .= ")";

        //distance
        $whereDistance = "AND r.distance BETWEEN '".$data['dist']['min']."' AND '".$data['dist']['max']."'";
        var_dump($whereDistance);

        //date
        $whereDate = "AND r.date_time BETWEEN '".$data['date']['min']."' AND '".$data['date']['max']."'";


        $rawSql = "
            SELECT r.id, r.name, r.distance, r.in_championship as inChampionship, r.date_time as dateTime,
            c.id as competitionId, c.name as competitionName,
            o.id as organizerId, o.name as organizerName,
            l.street, l.postCode, l.city, l.x, l.y
            FROM race_category rc 
            INNER JOIN race r ON rc.race_id = r.id
            INNER JOIN competition c ON r.competition_id = c.id
            INNER JOIN organizer o ON c.organizer_id = o.id
            INNER JOIN location l on c.location_id = l.id
            WHERE r.valid = 1
            ".$whereCategories.$wherePostCode.$whereDistance.$whereDate."
            GROUP By r.id
            ORDER BY r.date_time
            ";

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute([]);

        return $stmt->fetchAll();
    }

}
