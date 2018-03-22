<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Category;
use AppBundle\Entity\Organizer;

class CategoryRepository extends \Doctrine\ORM\EntityRepository
{
    public function categoriesByOrganizer($organizer)
    {
        return $this->createQueryBuilder('c')
            ->where('c.createBy = :organizer')
            ->setParameter('organizer', $organizer)
            ->orWhere('c.createBy = :admin')
            ->setParameter('admin', $this->getEntityManager()->getRepository(Organizer::class)->find(1))
            ;
    }

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
}
