<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 14/01/2018
 * Time: 08:21
 */

namespace AppBundle\Services;

use AppBundle\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;


class CategoryService
{
    private $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function getCategory($competitorYear, $gender)
    {
        $categories = $this->em->getRepository(Category::class)->findAll();

        foreach ($categories as $category) {

            if ($competitorYear <= $category->getAgeMin()
                && $competitorYear >= $category->getAgeMax()
                && $category->getSexe() == $gender
            )
                return $category;
        }
        return null;
    }
}