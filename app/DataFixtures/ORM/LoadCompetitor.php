<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/12/2017
 * Time: 22:01
 */

namespace app\DataFixtures\ORM;

use AppBundle\Entity\Competitor;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadCompetitor extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {

        for ($i = 0; $i<5; $i++)
        {
           $competitor = new Competitor();
           $competitor->setSexe('m');
           $competitor->setFirstName('FNCompetitor'.$i);
           $competitor->setLastName('LNCompetitor'.$i);
           $competitor->setDate(new \DateTime('14-03-1983'));
           $competitor->setUserId($i);

           $this->addReference('competitor'.$i, $competitor);

           $manager->persist($competitor);
           $manager->flush();
        }
    }

    public function getOrder()
    {
        return 1;
    }
}