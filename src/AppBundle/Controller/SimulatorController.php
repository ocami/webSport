<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 21/01/2018
 * Time: 18:59
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Race;
use AppBundle\Services\DbService;
use AppBundle\Services\RaceService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class SimulatorController extends Controller
{
    /**
     * @Route("simulator/users/{id}", name="simulator_generateUser")
     */
    public function generateDB($id)
    {
        $race = $this->getDoctrine()->getRepository(Race::class)->find($id);
        $this->get(RaceService::class)->generateChampionshipPoints($race);
        $test = "shiip";
        return $this->render('home/test2.html.twig', array('test' => $test));
    }

    /**
     * @Route("simulator/registration/{id}", name="simulator_registration")
     */
    public function simulateRegistration($id)
    {
        $race = $this->getDoctrine()->getRepository(Race::class)->find($id);
        $test = $this->get(DbService::class)->simulateRegistration($race);

        return $this->render('home/test2.html.twig', array('test' => $test));
    }

    /**
     * @Route("simulator/race/{id}", name="ranck_Race")
     */
    public function simulateRace($id)
    {
        $race = $this->getDoctrine()->getRepository(Race::class)->find($id);
        $this->get(DbService::class)->simulateRace($race);
        $test = 'simulateRace';

        return $this->render('home/test2.html.twig', array('test' => $test));
    }
}