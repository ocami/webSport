<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 14/01/2018
 * Time: 08:21
 */

namespace AppBundle\Services;

use AppBundle\Entity\Competition;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Location;
use AppBundle\Entity\Organizer;
use AppBundle\Services\UserService;

class CompetitionService
{
    private $em;
    private $us;


    public function __construct(EntityManagerInterface $em, UserService $us)
    {
        $this->em = $em;
        $this->us = $us;
    }


    public function create(Competition $competition, Organizer $organizer){

        $locationData = json_decode($competition->getLocationString(),true);

        $location = $this->em->getRepository(Location::class)->findOneByDataId($locationData['id']);

        if ($location==null)
        {
            $location = new Location();
            $location->setDataId($locationData['id']);
            $location->setStreet($locationData['street']);
            $location->setPostCode($locationData['postCode']);
            $location->setCity($locationData['city']);
            $location->setX($locationData['x']);
            $location->setY($locationData['y']);
        }

        $competition->setDateStart(new \DateTime($competition->getDateStart()));
        $competition->setDateEnd(new \DateTime($competition->getDateEnd()));
        $competition->setLocation($location);
        $competition->setOrganizer($organizer);

        $em = $this->em;
        $em->persist($location);
        $em->persist($organizer);
        $em->persist($competition);
        $em->flush();
    }




}