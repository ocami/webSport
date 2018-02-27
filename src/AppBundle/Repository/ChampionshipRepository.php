<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Championship;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class ChampionshipRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     */
    public function toString()
    {
        $c =  $this->createQueryBuilder('c')
            ->innerJoin('c.category','cat')
            ->select('c.name, cat.id')
            ->getQuery()->getResult();

        return $c;
    }
}
