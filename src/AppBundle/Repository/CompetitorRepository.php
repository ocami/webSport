<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Competitor;

/**
 * CompetitorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompetitorRepository extends EntityRepository
{
    /**
     * array strings [id,firstName,lastName,category]
     * @param  Competitor $competitor
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function toString(Competitor $competitor)
    {
        $c = $this->createQueryBuilder('c')
            ->innerJoin('c.category','cat')
            ->select('c.firstName,c.lastName,c.id,cat.name as category')
            ->where('c.id = :id')
            ->setParameter('id', $competitor)
            ->getQuery()->getSingleResult();

        return $c;
    }

    /**
     * array strings : statistic competitor accumulated for current year races
     * [allNbRace,icNbRace,ncNbRace,allDistance,icDistance,ncDistance,allChrono,icChrono,ncChrono,allSpeed,icSpeed,ncSpeed]
     * all : all races
     * nc : races not in championship
     * ic : races in championship
     * speed : average speed in km/h
     *
     * @param  Competitor $competitor
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function racesStat(Competitor $competitor)
    {
        $competitor = $competitor->getId();
        $y = date('Y', strtotime('now'));
        $dateStart = $y . '-01-01 00:00:00';
        $dateEnd = $y . '-12-31 23:59:59';

        $rawSqlAll = " SELECT
                        COUNT(r.id) as allNbRace,
                        SUM(CASE When r.in_championship = 1 Then 1 Else 0 End) as icNbRace,
                        SUM(CASE When r.in_championship = 0 Then 1 Else 0 End) as ncNbRace,
                        SUM(r.distance) as allDistance, 
                        SUM(CASE When r.in_championship = 1 Then r.distance Else 0 End) as icDistance,
                        SUM(CASE When r.in_championship = 0 Then r.distance Else 0 End) as ncDistance,
                        SUM(rc.chrono) as allChrono,
                        SUM(CASE When r.in_championship = 1 Then rc.chrono Else 0 End) as icChrono,
                        SUM(CASE When r.in_championship = 0 Then rc.chrono Else 0 End) as ncChrono
                        FROM race_competitor rc
                        INNER JOIN race r ON rc.race_id = r.id
                        WHERE rc.competitor_id = '" . $competitor . "'
                        AND r.date_time BETWEEN '" . $dateStart . "' AND '" . $dateEnd . "'";

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSqlAll);
        $stmt->execute([]);

        $data = $stmt->fetch();
        $data['allSpeed'] = 0;
        $data['icSpeed'] = 0;
        $data['ncSpeed'] = 0;

        if ($data['allChrono'])
            $data['allSpeed'] = round(($data['allDistance'] / $data['allChrono']) * 3600, 2);

        if ($data['icChrono'])
            $data['icSpeed'] = round(($data['icDistance'] / $data['icChrono']) * 3600, 2);

        if ($data['ncChrono'])
            $data['ncSpeed'] = round(($data['ncDistance'] / $data['ncChrono']) * 3600, 2);

        return $data;
    }

    /**
     * array strings[race[id,dateTime]] | null : next race which competitor must participate
     * @param  Competitor $competitor
     * @return array|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function nextRace(Competitor $competitor)
    {
        $competitor = $competitor->getId();
        $date = date('Y-m-d H:i:s', strtotime('now'));

        $rawSql = "SELECT r.id, r.date_time FROM race_competitor rc
                   INNER JOIN competitor c on rc.competitor_id = c.id
                   INNER JOIN race r on rc.race_id = r.id
                   WHERE c.id = " . $competitor . "
                   AND r.date_time > '" . $date . "'
                   ORDER BY r.date_time
                   LIMIT 1";

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute([]);

        return $stmt->fetch();
    }

    //for demo
    public function firstAll($nb)
    {
        $c = $this->createQueryBuilder('c')
            ->where('c.id <= :nb')
            ->setParameter('nb', $nb)
            ->getQuery()->getResult();

        return $c;
    }

    public function firstAllBygender($nb,$gender)
    {
        $c = $this->createQueryBuilder('c')
            ->where('c.id <= :nb')
            ->andWhere('c.gender = :gender')
            ->setParameter('nb', $nb)
            ->setParameter('gender', $gender)
            ->getQuery()->getResult();

        return $c;
    }
}
