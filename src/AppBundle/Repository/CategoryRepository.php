<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    /**
     * array  [Masculin[Category collection],Féminine[Category collection]]
     * @return array
     */
    public function categoriesByGender()
    {
        $categories = $this->findAll();

        $m = array();
        $f = array();
        foreach ($categories as $category) {
            if ($category->getGender() == 'm')
                array_push($m, $category);
            else
                array_push($f, $category);
        }

        return array('Masculin' => $m, 'Féminine' => $f);
    }

    /**
     * counted the number of categories
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function count()
    {

        $nbRC = $this->createQueryBuilder('c')
            ->select('COUNT(c)')
            ->getQuery()->getSingleScalarResult();

        return $nbRC;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     *
     */
    public function updateCompetitors()
    {

        $categories = $this->findAll();
        $string = '';

        foreach ($categories as $c) {
            $string .= " WHEN c.date >= '" . $c->getAgeMax() . "-01-01'  AND c.date <= '" . $c->getAgeMin() . "-31-12' AND c.gender = '" . $c->getGender() . "' THEN " . $c->getId();
        }

        $rawSqlAll = " UPDATE competitor c
                        SET c.category_id = (
                          CASE 
                            " . $string . "
                          END
                        )";

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSqlAll);
        $stmt->execute([]);

    }
}
