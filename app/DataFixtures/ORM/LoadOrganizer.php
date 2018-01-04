<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/12/2017
 * Time: 22:01
 */

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\organizer;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadOrganizer extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $organizer = new Organizer();
        $organizer->setName('LNOrganizer1');
        $organizer->setCode('FNOrganizer1');
        $organizer->setUserId(2);

        $this->addReference('orgnanizer1', $organizer );

        $manager->persist($organizer);
        $manager->flush();

    }

    public function getOrder()
    {
        return 1;
    }
}