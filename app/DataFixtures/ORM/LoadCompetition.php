<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/12/2017
 * Time: 22:01
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Competition;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadCompetition extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $competition = new Competition();
        $competition->setName('compet1');
        $competition->setCode('CMP01');
        $competition->setVille('Ville');
        $competition->setAdress('Adresse');
        $competition->setDep(35);
        $competition->setOrganizer($this->getReference('orgnanizer1'));

        $this->addReference('competition1', $competition );

        $manager->persist($competition);
        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }


}