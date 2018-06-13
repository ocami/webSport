<?php
/** TO DO
 * newToFlush
 * Créer service pour alléger la fonction ??
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use AppBundle\Services\GeoJsonService;
use AppBundle\Services\UserService;
use AppBundle\Entity\Race;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\CompetitionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Services\RaceService;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Services\CompetitionService;

class CompetitionController extends Controller
{
    /**
     * @Route("/competition/show/{id}", name="competition_show")
     */
    public function show(Competition $competition)
    {
        $competition = $this->get(UserService::class)->addUserDataInCompetition($competition);

        if($competition->getIsOrganizer())
            $races = $this->getDoctrine()->getRepository(Race::class)->findByCompetition($competition->getId());
        else
            $races = $this->getDoctrine()->getRepository(Race::class)->allValidByCompetition($competition->getId());

        $races = $this->get(RaceService::class)->postSelectAll($races);
        $races = $this->get(UserService::class)->addUserDataInRaces($races);

        return $this->render('competition/show.html.twig', array(
            'competition' => $competition,
            'races' => $races,
        ));
    }

    /**
     * @Route("/competition/show_all", name="competition_show_all")
     */
    public function showAll()
    {
        $competitions = $this->getDoctrine()->getRepository(Competition::class)->allValidByDate();
        $competitions = $this->get(CompetitionService::class)->postSelect($competitions);
        $competitions = $this->get(UserService::class)->addUserDataInCompetitions($competitions);

        return $this->render('competition/showList.html.twig', array(
            'competitions' => $competitions
        ));
    }

    /**
     * @Route("/competition/show_byOrganizer", name="competition_show_byOrganizer")
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function showByOrganizer()
    {
        $organizer = $this->get(UserService::class)->getOrganizer();
        $competitions = $this->getDoctrine()->getRepository(Competition::class)->byOrganizer($organizer->getId());
        $competitions = $this->get(CompetitionService::class)->postSelect($competitions);
        $competitions = $this->get(UserService::class)->addUserDataInCompetitions($competitions);

        return $this->render('competition/showList.html.twig', array(
            'competitions' => $competitions
        ));
    }

    /**
     * @Route("/competition/new", name="competition_new")
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function create(Request $request)
    {
        $competition = new Competition();
        $form = $this->createForm(CompetitionType::class, $competition);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $organizer = $this->get(UserService::class)->getOrganizer();

            $this->get(CompetitionService::class)->create($competition, $organizer);

            $request->getSession()->getFlashBag()->add('success', 'Compétition bien enregistrée');

            return $this->redirectToRoute('competition_show_byOrganizer');
        }

        return $this->render('competition/new.html.twig', array(
            'competition' => $competition,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/competition/edit/{id}", name="competition_edit")
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function update(Request $request, Competition $competition)
    {
        $form = $this->createForm(CompetitionType::class, $competition);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $organizer = $this->get(UserService::class)->getOrganizer();

            $this->get(CompetitionService::class)->create($competition, $organizer);

            $request->getSession()->getFlashBag()->add('success', $competition->getName().' modifiée');

            return $this->redirectToRoute('competition_show_byOrganizer');
        }

        return $this->render('competition/new.html.twig', array(
            'competition' => $competition,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/competition/getGeojson", options={"expose"=true}, name="competition_get_geojson")
     */
    public function getGeoJson()
    {
        return new JsonResponse($this->get(CompetitionService::class)->mapData());
    }
}