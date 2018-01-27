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
            $rc->setChrono(new \DateTime('1:22:30'));
            $rc->setChrono($this->tools->randomDate('2:00:00', '3:30:00', 'H:i:s'));
            $this->em->persist($rc);
        }

        if ($race->getInChampionship())
            $this->championshipSetPoints($race);

        $race->setPast(true);
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
            $rc = $this->em->getRepository(RaceCompetitor::class)->categoriesRanck($category, $race);
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

            $cc = $this->em->getRepository(ChampionshipCompetitor::class)->competitorsByCategoryOrderByPoints($championship);

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

            $rcByCategory = $this->em->getRepository(RaceCompetitor::class)->categoriesRanck2($category, $race);
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
                $cc->setRanck($cc->getRanck() + 1);
                $this->em->persist($cc);
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
            2 => 75,
            3 => 50,
            4 => 40,
            5 => 30,
            6 => 25,
            7 => 20,
            8 => 15,
            9 => 10,
            10 => 5
        );

        return $liste[$pos];
    }
}