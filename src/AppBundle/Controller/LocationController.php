<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

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
        $location = $request->get('location');

        var_dump($location);

        return 'bien recu';

        //return $this->render('home/test');

        /*

        $location->setDataId('monid');
        $location->setNumber('8');
        $location->setStreet('8');
        $location->setPostCode('8');
        $location->setCity('8');
        $location->setX(1.252525);
        $location->setY(1.252526);

        $em = $this->getDoctrine()->getManager();
        $em->persist($location);
        $em->flush();

        */
    }
  

}