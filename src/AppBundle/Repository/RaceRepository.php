<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Competition;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Race;
use phpDocumentor\Reflection\Types\Integer;

class RaceRepository extends EntityRepository
{

    /**
     * RaceCompetitor collection order by date
     * @return array
     */
    public function allValid()
    {
        $races = $this->createQueryBuilder('r')
            ->orderBy('r.dateTime')
            ->Where('r.valid = :isValid')
            ->setParameter('isValid', true)
            ->getQuery()->getResult();

        return $races;
    }

    /**
     * RaceCompetitor collection order by date
     * @param Competition $competition
     * @return array
     */
    public function allValidByCompetition(Competition $competition)
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
     * array strings
     * [race[id, name, distance, inChampionship, requestInChampionship ,dateTime],
     * competition[competitionId, competitionName],
     * organizer[organizerId, organizerName},
     * location[street, postCode, city, x, y]]
     * @param  int $idRace
     * @return array
     */
    public function toString($idRace)
    {
        $race = $this->createQueryBuilder('r')
            ->innerJoin('r.competition', 'c')
            ->innerJoin('c.organizer', 'o')
            ->innerJoin('c.location', 'l')
            ->select('r.id, r.name, r.distance, r.inChampionship, r.requestInChampionship ,r.dateTime, 
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
            ->setParameter('id', $idRace)
            ->getQuery()->getResult();

        $race['race'] = $race[0];
        unset($race[0]);

        $race['categories'] = $categories;

        return $race;
    }

    /**
     * @return int | null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
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

    /**
     * Search valid races of the array param :
     *
     *  ['categories']         array of id categories
     *  ['dep']                array of id departement
     *  ['inChampionship']     0|1
     *  ['dist']               -distance in km-
     *      ['min']            float
     *      ['max']            float
     *  ['date']
     *      ['min']            string (Y-m-d)
     *      ['max']            string (Y-m-d)
     *
     * @param array $data
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    Public function search(array $data)
    {

        $rawSql = "SELECT r.id
                    FROM race_category rc 
                    INNER JOIN race r ON rc.race_id = r.id
                    INNER JOIN competition c ON r.competition_id = c.id
                    INNER JOIN organizer o ON c.organizer_id = o.id
                    INNER JOIN location l on c.location_id = l.id
                    WHERE r.valid = 1 ";

        if ($data['categories']) {
            $whereCategories = "AND (rc.category_id = " . $data['categories'][0];
            for ($i = 0; $i < count($data['categories']); $i++) {
                if ($i >= 1)
                    $whereCategories .= " OR rc.category_id = " . $data['categories'][$i];
            }
            $whereCategories .= ")";

            $rawSql .= $whereCategories;
        }

        if ($data['dep']) {
            $wherePostCode = "AND (l.postCode LIKE '" . $data['dep'][0] . "%'";
            for ($i = 0; $i < count($data['dep']); $i++) {
                if ($i >= 1)
                    $wherePostCode .= " OR l.postCode LIKE '" . $data['dep'][$i] . "%'";
            }
            $wherePostCode .= ")";

            $rawSql .= $wherePostCode;
        }

        if (is_bool($data['inChampionship'])) {
            $whereChampionship = "AND r.in_championship = '" . $data['inChampionship'] . "'";
            $rawSql .= $whereChampionship;
        }

        if ($data['dist']) {
            if (!isset($data['dist']['min']))
                $data['dist']['min'] = 0;

            if (!isset($data['dist']['max']))
                $data['dist']['max'] = 999.9;

            $whereDistance = "AND r.distance BETWEEN '" . $data['dist']['min'] . "' AND '" . $data['dist']['max'] . "'";
            $rawSql .= $whereDistance;
        }

        if ($data['date']) {
            if (!isset($data['date']['min']))
                $data['date']['min'] = '2000-01-01';

            if (!isset($data['date']['max']))
                $data['date']['max'] = '2100-12-31';

            $whereDate = "AND r.date_time BETWEEN '" . $data['date']['min'] . " 00:00:00' AND '" . $data['date']['max'] . " 23:59:00'";
            $rawSql .= $whereDate;
        }

        $rawSql .= " GROUP BY r.id ORDER BY r.date_time";

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute([]);

        return $stmt->fetchAll();
    }
}
