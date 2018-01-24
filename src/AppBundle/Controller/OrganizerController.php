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
use AppBundle\Entity\Organizer;
use AppBundle\Form\OrganizerType;
use AppBundle\Services\UserService;
use AppBundle\Services\CodeService;

class OrganizerController extends Controller
{
    /**
     * @Route("/organizer/register"), name"organizer_register")
     */
    public function registerAction(Request $request, UserService $userService)
    {
        $organizer = new Organizer();
        $form = $this->createForm(OrganizerType::class, $organizer);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->get(UserService::class)->registerUserApp($organizer);
            $request->getSession()->getFlashBag()->add('notice', 'Votre compte competiteur est bien enregistré');

            return $this->redirectToRoute('index');
        }

        return $this->render('organizer/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}