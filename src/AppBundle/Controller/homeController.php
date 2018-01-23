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
use AppBundle\Services\RanckService;
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
     * @Route("/test/{id}", name="test")
     */
    public function test(Request $request, $id)
    {
        $race = $this->getDoctrine()->getRepository(Race::class)->find($id);

        $test = $this->get(RanckService::class)->championshipSetPoints($race);

        return $this->render('home/test.html.twig', array('test' => $test));
    }

}