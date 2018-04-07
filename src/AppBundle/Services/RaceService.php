<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 10/01/2018
 * Time: 20:18
 */

namespace AppBundle\Services;

use AppBundle\Entity\Category;
use AppBundle\Entity\Championship;
use AppBundle\Entity\Competition;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Organizer;
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCompetitor;
use Doctrine\ORM\EntityManagerInterface;


class RaceService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function generateCode($entity)
    {





    }

}