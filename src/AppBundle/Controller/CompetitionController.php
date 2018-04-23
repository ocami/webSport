<?php
/** TO DO
 * newToFlush
 * Créer service pour alléger la fonction ??
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use AppBundle\Entity\Location;
use AppBundle\Entity\Organizer;
use AppBundle\Services\CodeService;
use AppBundle\Services\GeoJsonService;
use AppBundle\Services\UserService;
use AppBundle\ServicesArg\AntiSpam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\CompetitionType;
use AppBundle\Form\CompetitionDescriptionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Services\RaceService;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Services\CompetitionService;

class CompetitionController extends Controller
{
    /**
     * @Route("/competition/show/{id}", name="competition_show")
     */
    public function showAction(Competition $competition)
    {
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
        $competitions = $this->getDoctrine()->getRepository(Competition::class)->allValidByDate();

        return $this->render('competition/showList.html.twig', array(
            'competitionsPassed' => $competitions['competitionsPassed'],
            'competitionsNoPassed' => $competitions['competitionsNoPassed']
        ));
    }

    /**
     * @Route("/competition/show_byOrganizer", name="competition_show_byOrganizer")
     */
    public function showByOrganizer()
    {
        $organizer = $this->get(UserService::class)->currentUserApp(Organizer::class);
        $competitions = $this->getDoctrine()->getRepository(Competition::class)->byOrganizer($organizer);

        return $this->render('competition/showList.html.twig', array(
            'competitionsPassed' => $competitions['competitionsPassed'],
            'competitionsNoPassed' => $competitions['competitionsNoPassed']
        ));
    }

    /**
     * @Route("/competition/new", name="competition_new")
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function newAction(Request $request)
    {
        $competition = new Competition();
        $form = $this->createForm(CompetitionType::class, $competition);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $organizer = $this->getDoctrine()->getRepository(Organizer::class)->findOneByUserId($this->getUser());

            $this->get(CompetitionService::class)->create($competition, $organizer);

            $request->getSession()->getFlashBag()->add('notice', 'Compétition bien enregistrée');

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
    public function edit(Request $request, Competition $competition)
    {

        $form = $this->createForm(CompetitionType::class, $competition);

//        $competition->setDateStart($competition->getDateStart()->format('Y-m-d'));
//        $competition->setDateEnd($competition->getDateEnd()->format('Y-m-d'));

      //  var_dump($competition);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $organizer = $this->getDoctrine()->getRepository(Organizer::class)->findOneByUserId($this->getUser());

            $this->get(CompetitionService::class)->create($competition, $organizer);

            $request->getSession()->getFlashBag()->add('notice', 'Compétition bien enregistrée');

            return $this->redirectToRoute('competition_show_byOrganizer');
        }

        return $this->render('competition/new.html.twig', array(
            'competition' => $competition,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/competition/getGeojson", name="competition_get_geojson")
     */
    public function getGeoJson(Request $request)
    {
        $competitor = $request->query->get('competitor');

        return new JsonResponse($this->get(GeoJsonService::class)->competitions($competitor));
    }
}