<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 14/01/2018
 * Time: 08:21
 */

namespace AppBundle\Services;

use AppBundle\Entity\Category;
use AppBundle\Entity\Competition;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCompetitor;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Services\UserService;


class RaceService
{
    private $em;
    private $us;

    public function __construct(EntityManagerInterface $em, UserService $us)
    {
        $this->em = $em;
        $this->us = $us;
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

    public function racesCompetitorCanEntry($races)
    {
        foreach ($races as $race) {
            $race = $this->competitorCanEntry($race);
        }

        return $races;
    }

    public function generateRanck(Race $race)
    {
        $rcs = $this->em->getRepository(RaceCompetitor::class)->crOrderByChrono($race);

        $i=0;
        foreach ($rcs as $rc)
        {
            $i++;
            $rc = new RaceCompetitor();
            $rc->setRanck($i);
        }

        return $rcs;
    }

    public function generateRanckByCategorie(Race $race)
    {
        $categoriesRanck = new \ArrayObject();

        foreach ($race->getCategories() as $category)
        {
            $categoryRanck = new \ArrayObject();
            $cr = $this->em->getRepository(RaceCompetitor::class)->categoriesRanck($category, $race);
            $categoryRanck->append($category);
            $categoryRanck->append($cr);

            $categoriesRanck->append($categoryRanck);
        }

        return $categoriesRanck;
    }
}