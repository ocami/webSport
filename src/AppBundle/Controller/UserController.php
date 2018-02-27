<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 30/12/2017
 * Time: 15:06
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class UserController extends Controller
{

    /**
     * @Route("/user/{toCreate}",requirements={"toCreate" = "competitor|organizer"}, name="redirectBeforeRegister")
     */
    public function redirectBeforeRegisterAction(Request $request, $toCreate)
    {
        //Enregistre si il s'agit d'une inscription Organisateur ou Competiteur
        $request->getSession()->set('toCreate', $toCreate);

        $user = $this->getUser();

        if (null === $user) {
            return $this->redirectToRoute('fos_user_registration_register');
        } else {
            return $this->redirectToRoute('redirectAfterRegister');
        }
    }

    /**
 * @Security("has_role('ROLE_USER')")
 * @Route("/redirectAfterRegister", name="redirectAfterRegister")
 */
    public function redirectAfterRegisterAction(Request $request)
    {
        $toCreate = $request->getSession()->get('toCreate');

        switch ($toCreate) {

            case 'competitor' :
                if ($this->get('security.authorization_checker')->isGranted('ROLE_COMPETITOR')) {
                    $request->getSession()->getFlashBag()->add('notice', 'Vous êtes déjà enregistré comme compétiteur, veuillez vous déconnecter avant de créer un nouveau compte');
                    return $this->render('home/logout.html.twig');
                }
                return $this->redirectToRoute('app_competitor_register');

            case 'organizer' :
                if ($this->get('security.authorization_checker')->isGranted('ROLE_ORGANIZER')) {
                    $request->getSession()->getFlashBag()->add('notice', 'Vous êtes déjà enregistré comme organisateur, veuillez vous déconnecter avant de créer un nouveau compte');
                    return $this->render('home/logout.html.twig');
                }
                return $this->redirectToRoute('app_organizer_register');

            default :
                $request->getSession()->getFlashBag()->add('notice', 'Enregistrement non valide, vous devez choisir de créer un compte competiteur ou organisateur');
                return $this->redirectToRoute('index');
        }
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/user/show/{id}", name="user_show")
     */
    public function Show(Request $request, User $user)
    {

    }

}