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
use AppBundle\Entity\RaceCompetitor;
use AppBundle\Entity\User;
use AppBundle\Entity\Organizer;
use AppBundle\Entity\Race;
use AppBundle\Repository\RaceCompetitorRepository;
use AppBundle\Services\DbService;
use AppBundle\Services\RaceService;
use AppBundle\Services\ToolsService;
use AppBundle\ServicesArg\AntiSpam;
use AppBundle\Repository\RaceRepository;
use AppBundle\Services\CodeService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Services\UserService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Services\MessageGenerator;


class homeController extends Controller
{

    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request)
    {
        $repo = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Competition');

        $competitions = $repo->findAll();

        return $this->render('home/index.html.twig', array('competitions' => $competitions));
    }

    /**
     * @Route("/becomeAdmin", name="becomeAdmin")
     */
    public function becomeAdmin()
    {
        $userManager = $this->get('fos_user.user_manager');

        $user = $this->getUser();

        $user->addRole('ROLE_ADMIN');

        $this->get(UserService::class)->refreshToken();

        $userManager->updateUser($user);

        return $this->render('home/test.html.twig');

    }

    /**
     * @Route("/userRole", name="userRole")
     */
    public function loginAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserBy(array('username' => 'lolo'));

        $user->addRole('ROLE_SUPER_ADMIN');

        $userManager->updateUser($user);

        return $this->render('home/test.html.twig', array('user' => $user));
    }

    /**
     * @Route("/test", name="test")
     */
    public function testAction()
    {

        $race = $this->getDoctrine()->getRepository(Race::class)->find(1);

        $race = new Race();
        $race->setCode('RaceTest');

        $category = new Category();
        $category->setCode('CategoryTest');
        $category->setSexe('m');
        $category->setAgeMin(2010);
        $category->setAgeMax(1995);

        $category2 = new Category();
        $category2->setCode('Category2Test');
        $category2->setSexe('f');
        $category2->setAgeMin(2012);
        $category2->setAgeMax(1990);

        $race->addCategory($category2);
        $race->addCategory($category);

        $race = $this->get(RaceService::class)->competitorCanEntry($race);

        $test = $race;

        return $this->render('home/test.html.twig', array('test' => $test));
    }

    /**
     * @Route("/test2", name="test2")
     */
    public function test2Action()
    {

        $competition = new Competition();

        $race = new Race();
        $race->setCode('RaceTest');

        $race2 = new Race();
        $race2->setCode('RaceTest_2');

        $category = new Category();
        $category->setCode('CategoryTest');
        $category->setSexe('m');
        $category->setAgeMin(2010);
        $category->setAgeMax(1995);

        $category2 = new Category();
        $category2->setCode('Category2Test_2');
        $category2->setSexe('f');
        $category2->setAgeMin(2012);
        $category2->setAgeMax(1990);

        $category3 = new Category();
        $category3->setCode('CategoryTest_3');
        $category3->setSexe('m');
        $category3->setAgeMin(1980);
        $category3->setAgeMax(1970);

        $category4 = new Category();
        $category4->setCode('Category2Test_4');
        $category4->setSexe('f');
        $category4->setAgeMin(2016);
        $category4->setAgeMax(2011);

        $race->addCategory($category);
        $race->addCategory($category2);
        $race2->addCategory($category3);
        $race2->addCategory($category4);

        $competition->addRace($race);
        $competition->addRace($race2);

        $races = $this->get(RaceService::class)->racesCompetitorCanEntry($competition->getRaces());

        $test = $races;

        return $this->render('home/test2.html.twig', array('test' => $test));
    }

    /**
     * @Route("/generateUser", name="generateUser")
     */
    public function generateDB()
    {
        $this->get(DbService::class)->generateUser();
        $test = $this->get(ToolsService::class)->randomDate();
        return $this->render('home/test.html.twig', array('test' => $test));
    }


    /**
     * @Route("/generateRace", name="generateRace")
     */
    public function generateRace()
    {
        $this->get(DbService::class)->generateRaces();
        $test = 'Races generate';

        return $this->render('home/test.html.twig', array('test' => $test));
    }


    /**
     * @Route("/generateCategories", name="generateCategories")
     */
    public function generateCategories()
    {
        $this->get(DbService::class)->genreateCategories();
        $test = 'Categoriess generate';

        return $this->render('home/test.html.twig', array('test' => $test));
    }


    /**
     * @Route("/simulateRegistration/{id}", name="simulateRegistration")
     */
    public function simulateRegistration($id)
    {
        $race = $this->getDoctrine()->getRepository(Race::class)->find($id);
        $test = $this->get(DbService::class)->simulateRegistration($race);

        return $this->render('home/test.html.twig', array('test' => $test));
    }


    /**
     * @Route("/simulateRace/{id}", name="simulateRace")
     */
    public function simulateRace($id)
    {
        $race = $this->getDoctrine()->getRepository(Race::class)->find($id);
        $this->get(DbService::class)->simulateRace($race);
        $test = 'simulateRace';

        return $this->render('home/test.html.twig', array('test' => $test));
    }

    /**
     * @Route("/categoryRanck/{id}", name="categoryRanck")
     */
    public function categoryRanck($id)
    {
        $race = $this->getDoctrine()->getRepository(Race::class)->find($id);
        $categorie = $this->getDoctrine()->getRepository(Category::class)->find(2);
        $cr=$this->getDoctrine()->getRepository(RaceCompetitor::class)->categoriesRanck($categorie, $race);
        $test = $cr;

        var_dump($cr);

        return $this->render('home/test.html.twig', array('test' => $test));
    }

    /**
     * @Route("/categoriesRanck/{id}", name="categoriesRanck")
     */
    public function categoriesRanck($id)
    {
        $race = $this->getDoctrine()->getRepository(Race::class)->find($id);
        $cr=$this->get(RaceService::class)->generateRanckByCategorie($race);
        $test = $cr;


        return $this->render('home/test.html.twig', array('test' => $test));
    }


}