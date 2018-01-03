<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 30/12/2017
 * Time: 15:06
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Service\UserService;
use AppBundle\Entity\Organizer;
use AppBundle\Form\OrganizerType;

class OrganizerController extends Controller
{

    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @Route("/organizer/register"), name"organizer_register")
     */
    public function registerAction(Request $request, UserService $userService)
    {
        $organizer = new Organizer();
        $form = $this->createForm(OrganizerType::class, $organizer);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $user->addRole('ROLE_ORGANIZER');

            $organizer->setUserId($user->getId());

            $em->persist($organizer);
            $em->persist($user);
            $em->flush();

            $token = $userService->refreshToken($user);
            $security = $this->container->get('security.token_storage');
            $security->setToken($token);

            $request->getSession()->getFlashBag()->add('notice', 'Votre compte competiteur est bien enregistré');

            return $this->render('home/index.html.twig');
        }

        return $this->render('organizer/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}