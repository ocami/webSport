<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 14/01/2018
 * Time: 08:21
 */

namespace AppBundle\Services;

use AppBundle\Entity\Competitor;
use AppBundle\Entity\Category;
use AppBundle\Entity\Race;
use Doctrine\ORM\EntityManagerInterface;

class RaceService
{
    private $em;
    private $us;
    private $cs;

    public function __construct(EntityManagerInterface $em, UserService $us, CodeService $cs)
    {
        $this->em = $em;
        $this->us = $us;
        $this->cs = $cs;
    }

    public function competitorCanEntry($race)
    {
        $competitor = $this->us->currentUserApp(Competitor::class);
        $competitorYear = $competitor->getDate()->format('Y');

        $race->setCompetitorCanEntry(false);

        foreach ($race->getCategories() as $category) {
            if ($competitorYear <= $category->getAgeMin() AND $competitorYear >= $category->getAgeMax()) {

                if ($competitorYear < $category->getAgeMin() AND $competitorYear > $category->getAgeMax()) {
                    $race->setCompetitorCanEntry(true);
                    return $race;
                }

            }
        }
        return $race;
    }

    public function competitorCanRegister($race)
    {
        $competitor = $this->us->currentUserApp(Competitor::class);
        $competitorYear = $competitor->getDate()->format('Y');

        $race->setCompetitorCanEntry(false);

        foreach ($race->getCategories() as $category) {
            if ($competitorYear <= $category->getAgeMin() AND $competitorYear >= $category->getAgeMax()) {

                if ($competitorYear < $category->getAgeMin() AND $competitorYear > $category->getAgeMax()) {
                    $race->setCompetitorCanEntry(true);
                    return $race;
                }

            }
        }
        return $race;
    }

    public function racesCompetitorCanEntry($races)
    {
        foreach ($races as $race) {
            $race = $this->competitorCanEntry($race);
        }
        return $races;
    }

    public function raceFlush(Race $race){

        $categoriesId = json_decode($race->getCategoriesString());
        $competition = $race->getCompetition();

        foreach($categoriesId as $category){
            $category  = $this->em->getRepository(Category::class)->find($category);

            $race->addCategory($category);

            if(!$competition->getCategories()->contains($category))
                $competition->addCategory($category);
        }

        $this->em->persist($race);
        $this->em->persist($competition);
        $this->em->flush();
        $this->cs->generateCode($race);
    }




}