<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 14/01/2018
 * Time: 08:21
 */

namespace AppBundle\Services;

use AppBundle\Entity\Competitor;
use Doctrine\ORM\EntityManagerInterface;

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

}