<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/12/2017
 * Time: 22:01
 */

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadCategory extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setSexe('m');
        $category->setAgeMax(1980);
        $category->setAgeMin(1990);
        $category->setName('category1');
        $category->setCode('CAT01');

        $this->addReference('category1', $category);

        $manager->persist($category);
        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}