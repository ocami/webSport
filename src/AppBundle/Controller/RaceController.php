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
use AppBundle\Entity\Race;
use AppBundle\Entity\Category;
use AppBundle\Entity\RaceCompetitor;
use AppBundle\Entity\User;
use AppBundle\Services\CodeService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Services\UserService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Post;
use AppBundle\Form\RaceType;
use AppBundle\Form\RaceChampionshipType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class RaceController extends Controller
{


    /**
     * @Route("/race/show/{id}", name="race_show")
     */
    public function showAction(Race $race)
    {
        $competitorsRace = $this->getDoctrine()->getRepository(RaceCompetitor::class)->findByRace($race);

        $racePasse = false;

        return $this->render('race/show.html.twig', array(
            'race' => $race,
            'cr' => $competitorsRace,
            'racePasse' => $racePasse
        ));
    }

    /**
     * @Security("has_role('ROLE_ORGANIZER')")
     * @Route("/race/new/{idCompetition}", name="race_new")
     */
    public function newAction(Request $request, $idCompetition)
    {
        $race = new Race();
        $organizer = $this->get(UserService::class)->currentUserApp(Organizer::class);
        $form = $this->createForm(RaceType::class, $race, array('organizer'=>$organizer));


        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $competition=$this->getDoctrine()->getRepository(Competition::class)->find($idCompetition);

            $race->setCompetition($competition);

            $em = $this->getDoctrine()->getManager();
            $em->persist($race);
            $em->flush();
            $this->get(CodeService::class)->generateCode($race);
            $request->getSession()->getFlashBag()->add('notice', 'Course bien enregistrée.');

            return $this->redirectToRoute('competition_show',array('idCompetition'=>$idCompetition));
        }

        return $this->render('race/new.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Security("has_role('ROLE_ORGANIZER')")
     * @Route("/race_championship/{idCompetition}", name="race_new_championship")
     */
    public function newInChampionshipAction(Request $request, $idCompetition)
    {
        $race = new Race();
        $form = $this->createForm(RaceChampionshipType::class, $race);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $competition=$this->getDoctrine()->getRepository(Competition::class)->find($idCompetition);
            $race->setCompetition($competition);

            foreach ($race->getChampionships() as $championship)
            {
                $race->addCategory($championship->getCategory());
            }


            $em = $this->getDoctrine()->getManager();
            $em->persist($race);
            $em->flush();
            $this->get(CodeService::class)->generateCode($race);
            $request->getSession()->getFlashBag()->add('notice', 'Course bien enregistrée.');

            return $this->redirectToRoute('competition_show',array('idCompetition'=>$idCompetition));
        }

        return $this->render('race/new.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Security("has_role('ROLE_ORGANIZER')")
     * @Route("/race/edit/{id}", name="race_edit")
     */
    public function editAction(Request $request, $id)
    {
        $race = $this->getDoctrine()->getRepository(Race::class)->find($id);
        $organizer = $this->get(UserService::class)->currentUserApp(Organizer::class);
        $form = $this->createForm(RaceType::class, $race, array('organizer'=>$organizer));

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($race);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Course bien modifiée.');

            return $this->redirectToRoute('competition_show',array(
                'idCompetition'=>$race->getCompetition()->getId()
            ));
        }

        return $this->render('race/new.html.twig', array('form' => $form->createView()));
    }
}