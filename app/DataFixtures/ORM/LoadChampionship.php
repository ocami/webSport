<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/12/2017
 * Time: 22:01
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Championship;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadChampionship extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $championship= new Championship();
        $championship->setName('championship1');
        $championship->setCategory($this->getReference('category1'));
        $championship->setCode('champ01');


        $this->addReference('championship1',$championship);

        $manager->persist($championship);
        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }


}