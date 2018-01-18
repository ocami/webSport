<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use AppBundle\Entity\Organizer;
use AppBundle\Services\CodeService;
use AppBundle\Services\UserService;
use AppBundle\ServicesArg\AntiSpam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\CompetitionType;
use AppBundle\Form\CompetitionDescriptionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Services\RaceService;

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
            ->getRepository('AppBundle:' . $class);

        return $competionRepository;
    }


    /**
     * @Route("/competition/show/{idCompetition}", name="competition_show")
     */
    public function showAction(Competition $idCompetition)
    {
        $competition = $this->getDoctrine()->getRepository(Competition::class)->find($idCompetition);
        $races = $competition->getRaces();
        // if user is competitior and if competitor is in category of race => reveal Entry Button
        if ($this->get('security.authorization_checker')->isGranted('ROLE_COMPETITOR'))
            $races = $this->get(RaceService::class)->racesCompetitorCanEntry($races);
        // reveal edit action if user is organizer of this competition
        $isOrganizer = $this->get(UserService::class)->isOrganizerComeptition($competition);

        return $this->render('competition/show.html.twig', array(
            'races' => $races,
            'competition' => $competition,
            'isOrganizer' => $isOrganizer
        ));
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
            $this->get(CodeService::class)->generateCode($competition);


            $request->getSession()->getFlashBag()->add('notice', 'Compétition bien enregistrée.');
            return $this->redirectToRoute('competition_show', array('idCompetition' => $competition->getId()));
        }
        return $this->render('competition/new.html.twig', array(
            'form' => $form->createView(),
            'organizer' => $organizer
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

            return $this->redirectToRoute('competition/show.html.twig', array('id' => $competition->getId()));
        }

        return $this->render('competition/new.html.twig', array('form' => $form->createView(), 'id' => $competition->getId()));
    }


    /**
     * @Security("has_role('ROLE_ORGANIZER')")
     * @Route("/competition/edit_description/{idCompetition}", name="competition_edit_description")
     */
    public function editDescriptionAction(Request $request, $idCompetition)
    {
        $competition = $this->competionRepository()->find($idCompetition);
        $form = $this->createForm(CompetitionDescriptionType::class, $competition);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            if ($this->get(AntiSpam::class)->isTextSpam($competition->getDescription()))
                throw new \Exception('Votre message a été détecté comme spam !');

            $em = $this->getDoctrine()->getManager();
            $em->persist($competition);
            $em->flush();

            return $this->redirectToRoute('competition_show', array('idCompetition' => $competition->getId()));
        }

        return $this->render('competition/new.html.twig', array('form' => $form->createView()));
    }


}