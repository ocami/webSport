<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Category;
use AppBundle\Entity\Organizer;

class CategoryRepository extends \Doctrine\ORM\EntityRepository
{
    public function categoriesByGender()
    {
        $categories = $this->findAll();

        $m = array();
        $f = array();
        foreach ($categories as $category){
            if($category->getSexe() == 'm')
                array_push($m, $category);
            else
                array_push($f, $category);
        }

        return array('Masculin'=>$m,'Féminine'=>$f);
    }

    public function count(){

        $nbRC = $this->createQueryBuilder('c')
            ->select('COUNT(c)')
            ->getQuery()->getSingleScalarResult();

        return $nbRC;
    }
}
