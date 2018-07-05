<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Address;
use AppBundle\Entity\Category;
use AppBundle\Entity\Competition;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Race;
use AppBundle\Repository\CompetitorRepository;
use AppBundle\Entity\RaceCompetitor;
use AppBundle\Form\RaceNewType;
use AppBundle\Services\CompetitionService;
use AppBundle\Services\CompetitorService;
use AppBundle\Services\DemoService;
use AppBundle\Services\ToolsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Services\UserService;
use AppBundle\Services\MessageGenerator;


class homeController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request)
    {
        $competitor = $this->get(UserService::class)->getCompetitor();
        $categories = $this->getDoctrine()->getRepository(Category::class)->categoriesByGender();
        $regions = $this->getDoctrine()->getRepository(Address::class)->departements();
        $competitions = $this->getDoctrine()->getRepository(Competition::class)->allValidFirstFive();
        $competitions = $this->get(UserService::class)->addUserDataInCompetitions($competitions);
        $competitions = $this->get(CompetitionService::class)->postSelect($competitions);

        return $this->render('home/index.html.twig', array(
            'competitor' => $competitor,
            'categories' => $categories,
            'competitions' => $competitions['future'],
            'regions' => $regions,
            'dataSearch' => null,
        ));
    }

    /**
     * @Route("/test", options={"expose"=true}, name="test")
     */
    public function test()
    {

        $this->getDoctrine()->getRepository(Category::class)->updateCompetitors();



        return $this->render('home/test.html.twig', array());
    }


}