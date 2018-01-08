<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\UserService;
use Symfony\Component\DependencyInjection\ContainerInterface;


class homeController  extends Controller
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

      return $this->render('home/index.html.twig',array('competitions'=>$competitions));
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

        return $this->render('home/test.html.twig', array('user'=>$user));
    }

}