<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/12/2017
 * Time: 22:01
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Race;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadRace extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $race = new race();
        $race->setCode("RAC01");
        $race->setName('race1');
        $race->setDate(new \DateTime('10-10-2018'));
        $race->setTime(new \DateTime('12:22:30'));
        $race->setKm(99);
        $race->setCompetition($this->getReference('competition1'));
        $race->setChampionship($this->getReference('championship1'));
        $race->addCategory($this->getReference('category1'));

        $this->addReference('race1',$race);

        $manager->persist($race);
        $manager->flush();

    }

    public function getOrder()
    {
        return 6;
    }


}