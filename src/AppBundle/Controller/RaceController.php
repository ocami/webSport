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


class RaceController  extends Controller
{

    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @Route("/races/{id}", name="race_show")
     */
    public function RaceSowAction(Request $request)
    {
      return $this->render('home/index.html.twig');
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

    /**
     * @Route("/attributRole", name="attributRole")
     */
    public function attributRoleAction(UserService $userService)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $this->getUser();
        $user->addRole('ROLE_SUPER_ADMIN');
        $userManager->updateUser($user);

        $token = $userService->refreshToken($user);

        $security = $this->container->get('security.token_storage');

        $security->setToken($token);

        return $this->render('home/test.html.twig', array('user'=>$user));

    }
}