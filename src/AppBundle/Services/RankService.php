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
use AppBundle\Repository\RaceCompetitorRepository;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Competitor;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RankService
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

    public function competitorNumberGenerator($race)
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

    public function importCompetitorsTimes($race)
    {
        $arrayChrono = $this->timeGenerator($race);
        $arrayCategories = [];
        $competitorRepo = $this->em->getRepository(Competitor::class);
        $rcRepo = $this->em->getRepository(RaceCompetitor::class);
        $ccRepo = $this->em->getRepository(ChampionshipCompetitor::class);
        $champRepo = $this->em->getRepository(Championship::class);

        asort($arrayChrono);

        foreach ($race->getCategories() as $cat) {
            $arrayCategories[$cat->getId()] = 1;
        }

        $i = 0;
        foreach ($arrayChrono as $key => $chrono) {
            $i++;
            $competitor = $competitorRepo->find($key);
            $rankCategory = $arrayCategories[$competitor->getCategory()->getId()]++;

            $rc = $rcRepo->onceByRaceCompetitor($race, $competitor);
            $rc->setChrono($chrono);
            $rc->setChronoString(gmdate("H:i:s", $chrono));
            $rc->setRank($i);
            $rc->setRankCategory($rankCategory);
            $this->em->persist($rc);

            if ($race->getInChampionship()) {

                $cc = $ccRepo->findOneBy(array('competitor' => $competitor));

                if ($cc == null) {
                    $championship = $champRepo->findOneBy(array('category' => $competitor->getCategory()));
                    $cc = new ChampionshipCompetitor();
                    $cc->setCompetitor($competitor);
                    $cc->setChampionship($championship);
                }

                $points = $this->pointGenerator($rankCategory);
                $rc->setPoints($points);
                $cc->setPoints($cc->getPoints() + $points);

                $this->em->persist($cc);
            }
        }

        if ($race->getInChampionship()) {
            $this->championshipUpdateRank($race);
        }
    }

    private function pointGenerator($pos)
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

    private function championshipUpdateRank($race)
    {
        $this->em->flush();

        foreach ($race->getCategories() as $category) {
            $championship = $this->em->getRepository(Championship::class)->findOneByCategory($category);
            $ccs = $this->em->getRepository(ChampionshipCompetitor::class)->allByChampionship($championship);

            $i = 0;
            foreach ($ccs as $cc) {
                $i++;
                $cc->setRank($i);
                $this->em->persist($cc);
            }
        }
    }

    // !!! FOR DEMONSTRATION !!!! //
    public function simulateEnrols($race)
    {
        $competitors = $this->em->getRepository(Competitor::class)->firstAll(100);

        foreach ($competitors as $competitor) {
            foreach ($race->getCategories() as $category) {
                if ($competitor->getCategory() == $category) {
                    $raceCompetitor = new RaceCompetitor();
                    $raceCompetitor->setRace($race);
                    $raceCompetitor->setCompetitor($competitor);
                    $this->em->persist($raceCompetitor);
                    break;
                }
            }
        }
        $this->em->flush();
    }
    
    private function timeGenerator($race)
    {
        $arrayChrono = [];

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

            $competitorLevel = $competitor->getLevel();

            $iCat = $arrayICat[$competitor->getCategory()->getId()];
            if (!$iCat == 0)
                $iCat = 1 + ($iCat / 100);
            else
                $iCat = 1;


            $level = $arrayILevel[$competitorLevel];

            $random = random_int(0, 25);
            $random = 1 + ($random / 100);

            $time = $base * $race->getDistance() * $iCat * $level * $random;

            $arrayChrono[$competitor->getId()] = $time;
        }
        return $arrayChrono;
    }
}