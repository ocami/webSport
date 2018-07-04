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
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCompetitor;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Competitor;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RankService
{
    private $ts;
    private $em;
    private $cs;
    private $tools;
    private $user;

    public function __construct(
        TokenStorageInterface $ts,
        EntityManagerInterface $em,
        CategoryService $cs,
        ToolsService $tools
    )
    {
        $this->ts = $ts;
        $this->em = $em;
        $this->cs = $cs;
        $this->tools = $tools;
        $this->user = $this->ts->getToken()->getUser();
    }


    public function importCompetitorsTimes($race)
    {
        $this->genrateTime($race);

        $raceCompetitors = $this->em->getRepository(RaceCompetitor::class)->allByRace($race);
        $arrayCountRaceCat = array();

        foreach ($race->getCategories() as $cat) {
            $arrayCountRaceCat[$cat->getId()] = 0;
        }

        $i = 0;
        $cpt = 0;
        foreach ($raceCompetitors as $rc) {
            $i++;
            $competitor = $rc->getCompetitor();

            $rc->setRank($i);

            foreach ($race->getCategories() as $cat) {

                $y = substr($competitor->getDate(), -10, 4);
                $competitorCat = $this->cs->getCategory($y, $competitor->getGender());
                $competitor->setCategory($this->cs->getCategory($y, $competitor->getGender()));

                if ($competitorCat == $cat) {
                    $cpt++;
                    $arrayCountRaceCat[$cat->getId()]++;
                    $count = $arrayCountRaceCat[$cat->getId()];

                    $rc->setRankCategory($count);

                    if ($race->getInChampionship())
                        $rc->setPoints($this->point($count));

                }
                $this->em->persist($rc);
            }
        }


        if ($race->getInChampionship())
            $this->championshipSetPoints($race);


        $race->setPassed(true);
        $race->setState(3);
        $this->em->persist($race);

        $this->em->flush();
    }

    public function genrateTime($race)
    {
        $raceCompetitors = $this->em->getRepository(RaceCompetitor::class)->findByRace($race);

        $base = 260;
        $arrayICat = array(
            1 => 24,
            2 => 16,
            3 => 8,
            4 => 0,
            5 => 8,
            6 => 32,
            7 => 24,
            8 => 16,
            9 => 8,
            10 => 16,
        );
        $arrayILevel = array(
            1 => 1.2,
            2 => 1,
            3 => 0.8,
        );

        foreach ($raceCompetitors as $rc) {

            $competitor = $rc->getCompetitor();
            $y = substr($competitor->getDate(), -10, 4);
            $competitorCat = $this->cs->getCategory($y, $competitor->getGender())->getId();


            $competitorLevel = $competitor->getLevel();

            $iCat = $arrayICat[$competitorCat];
            if (!$iCat == 0)
                $iCat = 1 + ($iCat / 100);
            else
                $iCat = 1;


            $level = $arrayILevel[$competitorLevel];

            $random = random_int(0, 25);
            $random = 1 + ($random / 100);

            $time = $base * $race->getDistance() * $iCat * $level * $random;

            $rc->setChrono($time);
            $rc->setChronoString(gmdate("H:i:s", $time));
            $this->em->persist($rc);
        }
        $this ->em->flush();
    }

    public function generateCompetitorsNumber($race)
    {
        $i = 0;
        $rc = $this->em->getRepository(RaceCompetitor::class)->allOrderByCompetitorLastName($race);
        foreach ($rc as $row) {
            $i++;
            $row->setNumber($i);
            $this->em->persist($row);
        }
        $race->setEnrol(false);
        $this->em->persist($race);
        $this->em->flush();
    }

    public function raceRank($race)
    {
        $rc = $this->em->getRepository(RaceCompetitor::class)->crOrderByChrono($race);

        $i = 0;
        foreach ($rc as $c) {
            $i++;
            $c->setRank($i);
        }

        return $rc;
    }

    public function raceCategoriesRank($race)
    {
        $categoriesRank = new \ArrayObject();

        foreach ($race->getCategories() as $category) {
            $rc = $this->em->getRepository(RaceCompetitor::class)->allByRaceCategoryToString($race, $category);
            $categoryRank = array(
                'category' => $category,
                'competitors' => $rc
            );
            $categoriesRank->append($categoryRank);
        }

        return $categoriesRank;
    }

    public function championshipsRank()
    {
        $championships = $this->em->getRepository(Championship::class)->findAll();
        $championshipsRank = new \ArrayObject();

        foreach ($championships as $championship) {

            $cc = $this->em->getRepository(ChampionshipCompetitor::class)->allByChampionshipToString($championship);

            $championshipRank = array(
                'championship' => $championship,
                'competitors' => $cc
            );
            $championshipsRank->append($championshipRank);
        }

        return $championshipsRank;
    }

    private function championshipSetPoints($race)
    {
        foreach ($race->getCategories() as $category) {

            $rcByCategory = $this->em->getRepository(RaceCompetitor::class)->allByRaceCategory($category, $race);
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
        $this->championshipUpdateRank($race);
    }

    private function championshipUpdateRank($race)
    {
        foreach ($race->getCategories() as $category) {
            $championship = $this->em->getRepository(Championship::class)->findOneByCategory($category);
            $ccs = $this->em->getRepository(ChampionshipCompetitor::class)->allByChampionship($championship);

            $i = 0;
            foreach ($ccs as $row) {

                $i++;
                $row->setRank($i);
                $this->em->persist($row);
            }
        }

        $this->em->flush();
    }

    private function point($pos)
    {
        if ($pos > 39)
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
}