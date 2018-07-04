<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 14/01/2018
 * Time: 08:21
 */

namespace AppBundle\Services;

use AppBundle\Entity\Category;
use AppBundle\Entity\Competitor;
use Doctrine\ORM\EntityManagerInterface;


class CompetitorService
{
    private $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getCompetitor($competitor)
    {
        $competitor = $this->em->getRepository(Competitor::class)->find($competitor);
        $competitor = $this->setCategoryCompetitor($competitor);

        return $competitor;
    }

    public function setCategoryCompetitor(Competitor $competitor)
    {
        $competitorYear = $competitor->getDateObject()->format('Y');

        $gender = $competitor->getGender();
        $categories = $this->em->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {

            if ($competitorYear <= $category->getAgeMin()
                && $competitorYear >= $category->getAgeMax()
                && $category->getGender() == $gender
            ){
                $competitor->setCategory($category);
                return $competitor;
            }
        }
        return $competitor;
    }

    public function setCategoryCompetitors($competitors){
        for($i=0;$i<count($competitors);$i++){
            $competitors[$i] = $this->setCategoryCompetitor($competitors[$i]);
        }

        return $competitors;
    }
}