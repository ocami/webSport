<?php
/** TO DO
 * newToFlush
 * Créer service pour alléger la fonction ??
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use AppBundle\Entity\Organizer;
use AppBundle\Services\GeoJsonService;
use AppBundle\Services\UserService;
use Proxies\__CG__\AppBundle\Entity\Race;
use SebastianBergmann\CodeCoverage\Report\Html\Renderer;
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
     *
     */
    public function show(Competition $competition)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ORGANIZER'))
            $races = $competition->getRaces();
        else
            $races = $this->getDoctrine()->getRepository(\AppBundle\Entity\Race::class)->findByValid(true);

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
    public function showAll()
    {
        $competitions = $this->get(CompetitionService::class)->showAll();

        return $this->render('competition/showList.html.twig', array(
            'for' => 'competitor',
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
        $competitions = $this->getDoctrine()->getRepository(Competition::class)->byOrganizer($organizer->getId());

        return $this->render('competition/showList.html.twig', array(
            'for' => 'organizer',
            'competitionsPassed' => $competitions['competitionsPassed'],
            'competitionsNoPassed' => $competitions['competitionsNoPassed']
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
    public function update(Request $request, Competition $competition)
    {
        $form = $this->createForm(CompetitionType::class, $competition);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $organizer = $this->getDoctrine()->getRepository(Organizer::class)->findOneByUserId($this->getUser());

            $this->get(CompetitionService::class)->create($competition, $organizer);

            $request->getSession()->getFlashBag()->add('notice', 'Compétition bien enregistrée');

            return $this->redirectToRoute('competition_show_byOrganizer');
        }

        return $this->render('competition/new.html.twig', array(
            'update' => true,
            'competition' => $competition,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/competition/getGeojson", name="competition_get_geojson")
     */
    public function getGeoJson()
    {
        return new JsonResponse($this->get(CompetitionService::class)->mapData());
    }
}