<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\CompetitionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;



class CompetitionController extends Controller
{

    private function competionRepository()
    {
        $competionRepository = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Competition');

        return $competionRepository;
    }

    private function repository($class)
    {
        $competionRepository = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:'.$class);

        return $competionRepository;
    }


    /**
     * @Route("/competition/show/{id}", name="competition_show")
     */
    public function showAction(Competition $competition)
    {
        return $this->render('competition/show.html.twig', array('competition' => $competition));
    }

    /**
     * @Route("/competition/show_all", name="competition_show_all")
     */
    public function showAllAction()
    {
        $competitions = $this->competionRepository()->findAll();

        return $this->render('competition/showList.html.twig', array('competitions' => $competitions));
    }

    /**
     * @Security("has_role('ROLE_ORGANIZER')")
     * @Route("/competition/new", name="competition_new")
     */
    public function newAction(Request $request)
    {
        $competition = new Competition();

        $form = $this->createForm(CompetitionType::class, $competition);
        $organizer = $this->repository('Organizer')->findOneByUserId($this->getUser());


        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {



            $organizer->addCompetition($competition);
            $competition->setOrganizer($organizer);
            $em = $this->getDoctrine()->getManager();
            $em->persist($competition);
            $em->persist($organizer);
            $em->flush();
    
            $request->getSession()->getFlashBag()->add('notice', 'Compétition bien enregistrée.');
            return $this->redirectToRoute('race_competition_show', array('idCompetition'=>$competition->getId()));
        }
        return $this->render('competition/new.html.twig', array(
            'form' => $form->createView(),
            'organizer'=> $organizer
        ));
    }

    /**
     * @Security("has_role('ROLE_ORGANIZER')")
     * @Route("/competition/edit/{id}", name="competition_edit")
     */
    public function editAction(Request $request, Competition $competition)
    {
        $form = $this->createForm(CompetitionType::class, $competition);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($competition);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Course bien enregistrée.');

            return $this->redirectToRoute('competition/show.html.twig', array('id'=>$competition->getId()));
        }

        return $this->render('competition/new.html.twig', array('form' => $form->createView()));
    }
}