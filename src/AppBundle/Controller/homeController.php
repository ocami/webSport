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
use AppBundle\Entity\Competition;
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCompetitor;
use AppBundle\Services\DbService;
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

        $jsonPastCompetition = array();

        $properties = array(
            'name' => 'la compétition',
            'description' => 'la description',
        );

        $geometry = array(
            "type" => "Point",
            "coordinates" => array(2.43896484375, 46.52863469527167)
        );

        $feature = array(
            "type" => "Feature",
            "properties" => $properties,
            "geometry" => $geometry,
        );

        array_push($jsonPastCompetition, $feature);


        return $this->render('home/index.html.twig', array('competitions' => $jsonPastCompetition));
    }


    /**
     * @Route("/geoJson", name="geoJson")
     */
    public function geoJson(Request $request)
    {

        $competitions = $this->getDoctrine()->getRepository(Competition::class)->findAll();

        $jsonPastCompetition = array();

        foreach ($competitions as $competition) {


            $properties = array(
                'name' => $competition->getName(),
                'description' => 'un trucR',
            );

            $geometry = array(
                "type" => "Point",
                "coordinates" => array($competition->getLocation()->getY(),$competition->getLocation()->getX())
            );


            $feature = array(
                "type" => "Feature",
                "properties" => $properties,
                "geometry" => $geometry,
            );

            array_push($jsonPastCompetition, $feature);

        }

        return new JsonResponse($jsonPastCompetition);
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
     * @Route("/data_import", name="data_import")
     */
    public function data_import()
    {
        $message = 'mon message';

        $ds = $this->get(DbService::class);

        $message = $ds->generateOrganizers();

        return $this->render('home/dataImport.html.twig', array(
            'message' => $message
        ));
    }

    /**
     * @Route("/test", name="test")
     */
    public function test()
    {
        $message = 'mon message';


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

        return $this->render('home/test.html.twig', array(
            'message' => $message
        ));
    }

}