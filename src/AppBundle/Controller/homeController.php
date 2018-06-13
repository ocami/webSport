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
use AppBundle\Form\RaceNewType;
use AppBundle\Services\CompetitionService;
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
        $competitions = $this->get(CompetitionService::class)->postSelect($competitions);
        $competitions = $this->get(UserService::class)->addUserDataInCompetitions($competitions);

        return $this->render('home/index.html.twig', array(
            'competitor' => $competitor,
            'categories' => $categories,
            'competitions' => $competitions,
            'regions' => $regions,
            'dataSearch' => null,
        ));
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(Request $request)
    {
        return $this->render('home/test.html.twig');
    }
}