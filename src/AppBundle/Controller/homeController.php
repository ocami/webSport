<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Championship;
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCompetitor;
use AppBundle\Services\RanckService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Services\UserService;
use AppBundle\Services\MessageGenerator;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class homeController extends Controller
{
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
    public function test()
    {
        /*
        $category = $this->getDoctrine()->getRepository(Category::class)->find(1);
        $race = $this->getDoctrine()->getRepository(Race::class)->find(1);

        $data = $this->getDoctrine()->getRepository(RaceCompetitor::class)->categoriesRanckToString($category, $race);

        return new JsonResponse($data);
        */

        $categories = $this->getDoctrine()->getRepository(Championship::class)->toString();

        return new JsonResponse($categories);
    }

    /**
     * @Route("/truc", name="truc")
     */
    public function truc(Request $request)
    {
        //Post parameters
        $race = $request->request->get('race');

        //GET parameters
        $race = $request->query->get('race');


        //$demo = new JsonResponse(array('id' => 'truc'));
        $demo = "La course :" . $race;
        $response = new Response(json_encode($demo));

        return $response;
    }

    /**
     * @Route("/table", name="table")
     */
    public function table(Request $request)
    {
        //GET parameters
        $race = $request->query->get('race');

        $response = new JsonResponse(
            array(
                array(
                    'userID' => '1',
                    'userName' => 'name1'),
                array(
                    'userID' => '2',
                    'userName' => 'deux'),
                array(
                    'userID' => '3',
                    'userName' => $race),
            )
        );

        return $response;
    }

    /**
     * @Route("/dataTable", name="dataTable")
     */
    public function dataTable(Request $request)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find(1);
        $race = $this->getDoctrine()->getRepository(Race::class)->find(1);

        $data = $this->getDoctrine()->getRepository(RaceCompetitor::class)->categoriesRanckToString($category, $race);

        return new JsonResponse($data);
    }

    /**
     * @Route("/categoryTable", name="categoryTable")
     */
    public function categoryTable(Request $request)
    {
        //$idRace = $request->query->get('race');
        $idCategory = $request->query->get('idCategory');

        $category = $this->getDoctrine()->getRepository(Category::class)->find($idCategory);
        $race = $this->getDoctrine()->getRepository(Race::class)->find(17);

        $data = $this->getDoctrine()->getRepository(RaceCompetitor::class)->categoriesRanckToString($category, $race);

        return new JsonResponse($data);
    }

    /**
     * @Route("/jsonTest", name="jsonTest")
     */
    public function jsonTest(Request $request)
    {
        return $this->render('home/jsonTest.html.twig');
    }

    /**
     * @Route("/typeaheadTest", name="typeaheadTest")
     */
    public function typeaheadTest(Request $request)
    {
        return $this->render('home/typeaheadTest.html.twig');
    }
}