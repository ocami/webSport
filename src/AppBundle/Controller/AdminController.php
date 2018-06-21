<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Race;
use AppBundle\Services\RaceService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Services\MessageGenerator;

class AdminController extends Controller
{

    /**
     * @Route("/admin/races", name="admin_races")
     */
    public function races(Request $request)
    {
        $races = $this->getDoctrine()->getRepository(Race::class)->findBySupervised(false);

        return $this->render('admin/races.html.twig', array(
            'races' => $races
        ));
    }

    /**
     * @Route("/admin/race/supervise",options={"expose"=true}, name="race_admin_supervise")
     */
    public function raceSupervise(Request $request)
    {
        $this->get(RaceService::class)->adminSuperviseUpdate($request->query->all());

        return new JsonResponse('super');
    }
}