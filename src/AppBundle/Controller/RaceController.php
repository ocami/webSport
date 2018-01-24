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
use AppBundle\Services\DbService;
use AppBundle\Services\UserService;
use AppBundle\Services\EntityService;
use AppBundle\Services\RanckService;
use Proxies\__CG__\AppBundle\Entity\RaceCompetitor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
        $isOrganizer = $this->get(UserService::class)->isOrganizerComeptition($race->getCompetition());

        return $this->render('race/show.html.twig', array(
            'race' => $race,
            'isOrganizer' => $isOrganizer
        ));
    }

    /**
     * @Route("/race/showRanck/{id}", name="race_ranck_show")
     */
    public function showRanckAction(Race $race)
    {
        $race = $this->getDoctrine()->getRepository(Race::class)->find($race);
        $gr = $this->getDoctrine()->getRepository(RaceCompetitor::class)->rcOrderByChrono($race);
        $cr = $this->get(RanckService::class)->raceCategoriesRanck($race);
        $isOrganizer = $this->get(UserService::class)->isOrganizerComeptition($race->getCompetition());

        return $this->render('race/showRanck.html.twig', array(
            'race' => $race,
            'generalRanck' => $gr,
            'categoriesRanck' => $cr,
            'isOrganizer' => $isOrganizer
        ));
    }

    /**
     * @Security("has_role('ROLE_ORGANIZER')")
     * @Route("/race/new/{id}", name="race_new")
     */
    public function newAction(Request $request, Competition $competition)
    {
        $race = new Race();
        $organizer = $this->get(UserService::class)->currentUserApp(Organizer::class);
        $form = $this->createForm(RaceType::class, $race, array('organizer' => $organizer));


        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $race->setCompetition($competition);
            $this->get(EntityService::class)->create($race);

            $request->getSession()->getFlashBag()->add('notice', 'Course bien enregistrée.');
            return $this->redirectToRoute('competition_show', array('id' => $competition->getId()));
        }

        return $this->render('race/new.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Security("has_role('ROLE_ORGANIZER')")
     * @Route("/race_championship/{id}", name="race_new_championship")
     */
    public function newInChampionshipAction(Request $request, Competition $competition)
    {
        $race = new Race();
        $form = $this->createForm(RaceChampionshipType::class, $race);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $race->setCompetition($competition);
            $race->setInChampionship(true);

            foreach ($race->getChampionships() as $championship) {
                $race->addCategory($championship->getCategory());
            }

            $this->get(EntityService::class)->create($race);

            $request->getSession()->getFlashBag()->add('notice', 'Course bien enregistrée.');
            return $this->redirectToRoute('competition_show', array('id' => $competition->getId()));
        }

        return $this->render('race/new.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Security("has_role('ROLE_ORGANIZER')")
     * @Route("/race/edit/{id}", name="race_edit")
     */
    public function editAction(Request $request, Race $race)
    {
        $organizer = $this->get(UserService::class)->currentUserApp(Organizer::class);
        $form = $this->createForm(RaceType::class, $race, array('organizer' => $organizer));

        if ($race->getInChampionship() == true)
            $form = $this->createForm(RaceChampionshipType::class, $race);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            if ($race->getInChampionship() == true) {
                foreach ($race->getCategories() as $category)
                    $race->removeCategory($category);

                foreach ($race->getChampionships() as $championship) {
                    $race->addCategory($championship->getCategory());
                }
            }

            $this->get(EntityService::class)->update($race);

            $request->getSession()->getFlashBag()->add('notice', 'Course bien modifiée.');

            return $this->redirectToRoute('competition_show', array(
                'id' => $race->getCompetition()->getId()
            ));
        }

        return $this->render('race/new.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Security("has_role('ROLE_ORGANIZER')")
     * @Route("/race/simulateEnrol/{id}", name="race_simulateEnrol")
     */
    public function simulateEnrol(Request $request, Race $race)
    {
        $this->get(DbService::class)->simulateRaceEnrols($race);

        $request->getSession()->getFlashBag()->add('notice', 'Inscriptions enregistrées');
        return $this->redirectToRoute('race_show',array('id'=>$race->getId()));
    }

    /**
     * @Security("has_role('ROLE_ORGANIZER')")
     * @Route("/race/ranckEnrolClosed/{id}", name="race_enrol_closed")
     */
    public function ranckEnrolClosed(Request $request, Race $race)
    {
        $this->get(RanckService::class)->generateCompetitorsNumber($race);

        $request->getSession()->getFlashBag()->add('notice', 'Cloture des inscription enregistrées');

        return $this->redirectToRoute('race_show',array('id'=>$race->getId()));
    }

    /**
     * @Security("has_role('ROLE_ORGANIZER')")
     * @Route("/race/competitorsTimes/{id}", name="race_competitorsTimes")
     */
    public function importCompetitorsTimes(Request $request, Race $race)
    {
        $this->get(RanckService::class)->importCompetitorsTimes($race);

        return $this->redirectToRoute('race_show',array('id'=>$race->getId()));
    }
}