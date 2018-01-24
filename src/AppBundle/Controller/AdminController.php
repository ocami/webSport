<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Category;
use AppBundle\Entity\Championship;
use AppBundle\Entity\Competition;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\User;
use AppBundle\Entity\Organizer;
use AppBundle\Entity\Race;
use AppBundle\Services\RaceService;
use AppBundle\Services\DbService;
use AppBundle\ServicesArg\AntiSpam;
use AppBundle\Repository\RaceRepository;
use AppBundle\Services\CodeService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Services\UserService;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
}