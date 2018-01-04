<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/12/2017
 * Time: 22:01
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Race;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\RaceCompetitor;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadRaceCompetitor extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {

        $raceCompetitor = new RaceCompetitor();
        $race = $this->getReference('race1');

        for ($i = 0; $i<5; $i++)
        {
           $raceCompetitor->setRace($race);
           $raceCompetitor->setCompetitor($this->getReference('competitor'.$i));
           $raceCompetitor->setNumber($i);
        }

        $manager->persist($raceCompetitor);
        $manager->flush();

    }

    public function getOrder()
    {
        return 7;
    }


}