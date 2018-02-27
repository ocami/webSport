<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 17:05
 */

namespace AppBundle\Services;

use AppBundle\Entity\Category;
use AppBundle\Entity\Championship;
use AppBundle\Entity\ChampionshipCompetitor;
use AppBundle\Entity\RaceCompetitor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class RanckService
{
    private $ts;
    private $em;
    private $tools;
    private $user;

    public function __construct(
        TokenStorageInterface $ts,
        EntityManagerInterface $em,
        ToolsService $tools
    )
    {
        $this->ts = $ts;
        $this->em = $em;
        $this->tools = $tools;
        $this->user = $this->ts->getToken()->getUser();
    }

    public function generateCompetitorsNumber($race)
    {
        $i = 0;
        $rc = $this->em->getRepository(RaceCompetitor::class)->competitorsEnrolByLastName($race);
        foreach ($rc as $row) {
            $i++;
            $row->setNumber($i);
            $this->em->persist($row);
        }
        $race->setEnrol(false);
        $this->em->persist($race);
        $this->em->flush();
    }

    public function importCompetitorsTimes($race)
    {
        $raceCompetitors = $this->em->getRepository(RaceCompetitor::class)->findByRace($race);

        foreach ($raceCompetitors as $rc) {
            $time = $this->tools->randomDate('2:00:00', '3:30:00', 'H:i:s');
            $rc->setChrono($time);
            $rc->setChronoString($time->format('H:i:s'));
            $this->em->persist($rc);
        }

        $this->em->flush();

        $raceCompetitors = $this->em->getRepository(RaceCompetitor::class)->rcOrderByChrono($race);

        $i=0;
        foreach ($raceCompetitors as $rc) {
            $i++;
            $rc->setRanck($i);
            $this->em->persist($rc);
        }

        if ($race->getInChampionship())
            $this->championshipSetPoints($race);

        $race->setPassed(true);
        $this->em->flush();
    }

    public function raceRanck($race)
    {
        $rc = $this->em->getRepository(RaceCompetitor::class)->crOrderByChrono($race);

        $i = 0;
        foreach ($rc as $c) {
            $i++;
            $c->setRanck($i);
        }

        return $rc;
    }

    public function raceCategoriesRanck($race)
    {
        $categoriesRanck = new \ArrayObject();

        foreach ($race->getCategories() as $category) {
            $rc = $this->em->getRepository(RaceCompetitor::class)->categoriesRanckToString($category, $race);
            $categoryRanck = array(
                'category' => $category,
                'competitors' => $rc
            );
            $categoriesRanck->append($categoryRanck);
        }

        return $categoriesRanck;
    }

    public function championshipsRanck()
    {
        $championships = $this->em->getRepository(Championship::class)->findAll();
        $championshipsRanck = new \ArrayObject();

        foreach ($championships as $championship) {

            $cc = $this->em->getRepository(ChampionshipCompetitor::class)->competitorsOrderByPointsToString($championship);

            $championshipRanck = array(
                'championship' => $championship,
                'competitors' => $cc
            );
            $championshipsRanck->append($championshipRanck);
        }

        return $championshipsRanck;
    }

    private function championshipSetPoints($race)
    {
        foreach ($race->getCategories() as $category) {

            $rcByCategory = $this->em->getRepository(RaceCompetitor::class)->categoriesRanck($category, $race);
            $championship = $this->em->getRepository(Championship::class)->findOneByCategory($category);

            $i = 0;
            foreach ($rcByCategory as $row) {
                $cc = $this->em->getRepository(ChampionshipCompetitor::class)
                    ->findOneBy(array('championship' => $championship->getId(), 'competitor' => $row->getCompetitor()));

                if ($cc == null) {
                    $cc = new ChampionshipCompetitor();
                    $cc->setCompetitor($row->getCompetitor());
                    $cc->setChampionship($championship);
                }

                $i++;
                $cc->setPoints($cc->getPoints() + $this->point($i));
                $this->em->persist($cc);
            }
        }

        $this->em->flush();
        $this->championshipUpdateRanck($race);
    }

    private function championshipUpdateRanck($race)
    {
        foreach ($race->getCategories() as $category) {
            $championship = $this->em->getRepository(Championship::class)->findOneByCategory($category);
            $ccs = $this->em->getRepository(ChampionshipCompetitor::class)->ccOrderByPoints($championship);

            $i = 0;
            foreach ($ccs as $row) {

                $i++;
                $row->setRanck($i);
                $this->em->persist($row);
            }
        }

        $this->em->flush();
    }

    private function point($pos)
    {
        if ($pos > 10)
            return 0;

        $liste = array(
            1 => 100,
            2 => 90,
            3 => 80,
            4 => 70,
            5 => 60,
            6 => 50,
            7 => 45,
            8 => 40,
            9 => 35,
            10 => 30,
            11 => 29,
            12 => 28,
            13 => 27,
            14 => 26,
            15 => 25,
            16 => 24,
            17 => 23,
            18 => 22,
            19 => 21,
            20 => 20,
            21 => 19,
            22 => 18,
            23 => 17,
            24 => 16,
            25 => 15,
            26 => 14,
            27 => 13,
            28 => 12,
            29 => 11,
            30 => 10,
            31 => 9,
            32 => 8,
            33 => 7,
            34 => 6,
            35 => 5,
            36 => 4,
            37 => 3,
            38 => 2,
            39 => 1
        );

        return $liste[$pos];
    }

    public function raceCategorieRanck($race, $category)
    {
        $data = new \ArrayObject();
        $rc = $this->em->getRepository(RaceCompetitor::class)->categoriesRanckToString($category, $race);
        $data = array();



        return $rc;
    }
}