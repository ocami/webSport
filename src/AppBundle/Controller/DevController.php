<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use AppBundle\Entity\Address;
use AppBundle\Entity\Race;
use AppBundle\Entity\Category;
use AppBundle\Entity\RaceCompetitor;
use AppBundle\Form\RaceType;
use AppBundle\Services\CategoryService;
use AppBundle\Services\RaceService;
use AppBundle\Services\DevService;
use AppBundle\Services\UserService;
use AppBundle\Services\RanckService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class DevController extends Controller
{
    /**
     * @Route("/dev", name="dev_index")
     */
    public function home()
    {
        $cote = "'";

        var_dump("c".$cote."est l".$cote."expression");

        return $this->render('dev/index.html.twig', array());
    }



    /**
     * @Route("/dev/request/action", options={"expose"=true}, name="dev_request")
     */
    public function request(Request $request)
    {
        $action = $request->get('action');

        $stmt = $this->get(DevService::class)->request($action);

        if ($stmt)
            $request->getSession()->getFlashBag()->add('success', $stmt);
        else
            $request->getSession()->getFlashBag()->add('alert', 'erreur');

        return $this->redirectToRoute('dev_index');
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


}