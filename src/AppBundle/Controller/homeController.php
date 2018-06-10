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
use AppBundle\Entity\Championship;
use AppBundle\Entity\Competition;
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCompetitor;
use AppBundle\Form\RaceNewType;
use AppBundle\Repository\AddressRepository;
use AppBundle\Services\CompetitionService;
use AppBundle\Services\DbService;
use AppBundle\Services\RaceService;
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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Competitor;
use Symfony\Component\Validator\Constraints\Time;


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
     * @Route("/becomeAdmin", name="becomeAdmin")
     */
    public function becomeAdmin()
    {
        $userManager = $this->get('fos_user.user_manager');

        $user = $this->getUser();

        $user->addRole('ROLE_ADMIN');

        $this->get(UserService::class)->refreshToken();

        $userManager->updateUser($user);

        return $this->render('home/test.html.twig', array(
            'message' => "Role admin",
        ));

    }

    /**
     * @Route("/data_import", name="data_import")
     */
    public function data_import()
    {
        $message = 'categories ok';

        $ds = $this->get(DbService::class);

        $message = $ds->generateChampionship();

        return $this->render('home/dataImport.html.twig', array(
            'message' => $message
        ));
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(Request $request)
    {

        /* $em = $this->getDoctrine()->getManager();
         $competitors = $this->getDoctrine()->getRepository(Competitor::class)->findAll();

         $i = 0;
         for ($y = 0; $y < count($competitors); $y++) {
             $i++;

             $competitors[$y]->setLevel($i);
             $em->persist($competitors[$y]);

             if ($i == 3)
                 $i = 0;
         }
         $em->flush();*/
        /*        $race = new Race();
                $race->setDistance(10);

                $competitor = new Competitor();
                $competitor->setCategory(4);
                $competitor->setLevel(3);
                $competitor->setFirstName('le balaise');

                $base = 260;

                $arrayICat = array(
                    1 => 24,
                    2 => 16,
                    3 => 8,
                    4 => 0,
                    5 => 8,
                    6 => 32,
                    7 => 24,
                    8 => 16,
                    9 => 8,
                    10 => 16,
                );

                $iCat = $arrayICat[$competitor->getCategory()];
                if(!$iCat == 0)
                    $iCat = 1+ ($iCat/100);
                else
                    $iCat = 1;

                $arrayILevel = array(
                    1 => 1.2,
                    2 => 1,
                    3 => 0.8,
                );
                $level = $arrayILevel[$competitor->getLevel()];

                $random = random_int(0, 25);
                $random = 1 +  ($random/100);


                $time = $base * $race->getDistance() *$iCat * $level * $random;
                var_dump(gmdate("H:i:s", $time));
                var_dump(new \DateTime(gmdate("H:i:s", $time)));
                $time = date("H:i:s", $time);*/
        //        $time = $base * $race->getDistance();
//        $time = date("H:i:s", $time);
//
//        var_dump('base');
//        var_dump($time);
//
//        //$time = $base * $race->getDistance() * $iCat * $random;
//        $time = $base * $race->getDistance() * $iCat;
//        $time = date("H:i:s", $time);
//
//        var_dump('category');
//        var_dump($iCat);
//        var_dump($time);
//
//        $time = $base * $race->getDistance() * $iCat * $level;
//        $time = date("H:i:s", $time);
//
//        var_dump('level');
//        var_dump($level);
//        var_dump($time);
//
//        $time = $base * $race->getDistance() *$iCat * $level * $random;
//        $time = date("H:i:s", $time);
//
//        var_dump('random');
//        var_dump($random);
//        var_dump($time);
        /* distance course
                $str = file_get_contents('..\web\gpx\trace.json');
                $trace = json_decode($str, true);
                $tabDistance = $trace[0]['tabDistance'];
                $km = 0;
                for($i=0;$i < count($tabDistance);$i++)
                {
                    for ($x=0;$x<count($tabDistance[$i]);$x++)
                    {
                        $km += $tabDistance[$i][$x];
                        //var_dump($tabDistance[$i][$x]);

                    }

                }
                //var_dump($km);
        */


        return $this->render('home/test.html.twig', array(

        ));
    }

    /**
     * @Route("/load", name="load")
     */
    public function load()
    {

        return $this->render('home/load.html.twig');
    }

    /**
     * @Route("/map", name="map")
     */
    public function map()
    {

        return $this->render('home/map.html.twig');
    }
}