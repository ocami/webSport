<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Race;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Services\MessageGenerator;


class AdminController extends Controller
{
    /**
     * @Route("/admin/index", name="admin_index")
     */
    public function indexAction(Request $request)
    {
        $races = $this->getDoctrine()->getRepository(Race::class)->findAll();

        return $this->render('admin/index.html.twig', array(
            'races' => $races
        ));
    }

    /**
     * @Route("/admin/races", name="admin_races")
     */
    public function racesAction(Request $request)
    {
        $races = $this->getDoctrine()->getRepository(Race::class)->findAll();

        return $this->render('admin/races.html.twig', array(
            'races' => $races
        ));
    }
}