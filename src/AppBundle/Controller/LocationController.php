<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Organizer;
use AppBundle\Entity\Location;
use AppBundle\Entity\Competition;
use AppBundle\Services\EntityService;
use AppBundle\Services\RanckService;
use Proxies\__CG__\AppBundle\Entity\LocationCompetitor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;



class LocationController extends Controller
{
    /**
     * @Route("/location/new", name="location_new")
     */
    public function newAction(Request $request)
    {
        $Data = $request->query->get('location');
        $locationData = $Data['location'];
        $competitionData = $Data['competition'];

        var_dump($Data);

        $location = $this->getDoctrine()->getRepository(Location::class)->findOneByDataId($locationData['id']);

        if ($location==null)
        {
            $location = new Location();
            $location->setDataId($locationData['id']);
            $location->setNumber(0);
            $location->setStreet($locationData['street']);
            $location->setPostCode($locationData['postCode']);
            $location->setCity($locationData['city']);
            $location->setX($locationData['x']);
            $location->setY($locationData['y']);
        }


        $organizer = $this->getDoctrine()->getRepository(Organizer::class)->findOneByUserId($this->getUser());

        $competition = new Competition();

        $competition->setName($competitionData['name']);
        $competition->setDateStart(new \DateTime($competitionData['dateStart']));
        $competition->setDateEnd(new \DateTime($competitionData['dateEnd']));
        $competition->setLocation($location);
        $competition->setOrganizer($organizer);

        $em = $this->getDoctrine()->getManager();
        $em->persist($location);
        $em->persist($organizer);
        $em->persist($competition);
        $em->flush();

        return new JsonResponse($Data);
    }
  

}