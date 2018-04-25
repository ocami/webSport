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
use AppBundle\Form\RaceNewType;
use AppBundle\Services\CompetitionService;
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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Competitor;


class homeController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request)
    {
        $competitor = null;
        if ($this->get('security.authorization_checker')->isGranted('ROLE_COMPETITOR'))
            $competitor = $this->get(UserService::class)->setCategoryCompetitor();

        return $this->render('home/index.html.twig', array(
            'competitor' => $competitor,
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

        return $this->render('home/test.html.twig');

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
        $message = 'mon message';

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
                'message' => $message,
            ));


        return $this->render('home/test.html.twig', array(
            'message' => $message,
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