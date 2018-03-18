<?php
/** TO DO

    * newToFlush
         Créer service pour alléger la fonction ??


 */

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use AppBundle\Entity\Location;
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
use Symfony\Component\HttpFoundation\JsonResponse;

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
        $competitions = $this->getDoctrine()->getRepository(Competition::class)->byDate();

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
     * @Route("/competition/new_form", name="competition_new_form")
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function newForm()
    {
        return $this->render('competition/new.html.twig');
    }

    /**
     * @Route("/competition/new_toFlush", name="competition_new_toFlush")
     */
    public function newToFlush(Request $request)
    {
        $Data = $request->query->get('location');
        $locationData = $Data['location'];
        $competitionData = $Data['competition'];

        $location = $this->getDoctrine()->getRepository(Location::class)->findOneByDataId($locationData['id']);

        if ($location==null)
        {
            $location = new Location();
            $location->setDataId($locationData['id']);
            $location->setNumber(0);
            $location->setStreet($locationData['street']);
            $location->setPostCode($locationData['postCode']);
            $location->setCity($locationData['city']);
            $location->setX($locationData['x']);
            $location->setY($locationData['y']);
        }

        $organizer = $this->getDoctrine()->getRepository(Organizer::class)->findOneByUserId($this->getUser());

        $competition = new Competition();

        $competition->setName($competitionData['name']);
        $competition->setDateStart(new \DateTime($competitionData['dateStart']));
        $competition->setDateEnd(new \DateTime($competitionData['dateEnd']));
        $competition->setLocation($location);
        $competition->setOrganizer($organizer);

        $em = $this->getDoctrine()->getManager();
        $em->persist($location);
        $em->persist($organizer);
        $em->persist($competition);
        $em->flush();

        return new JsonResponse($Data);
    }

    /**
     * @Route("/competition/edit/{id}", name="competition_edit")
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function edit(Request $request, Competition $competition)
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
     * @Route("/competition/edit_description/{id}", name="competition_edit_description")
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function editDescription(Request $request, Competition $competition)
    {
        $form = $this->createForm(CompetitionDescriptionType::class, $competition);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            if ($this->get(AntiSpam::class)->isTextSpam($competition->getDescription()))
                throw new \Exception('Votre message a été détecté comme spam !');

            $em = $this->getDoctrine()->getManager();
            $em->persist($competition);
            $em->flush();

            return $this->redirectToRoute('competition_show', array('id' => $competition->getId()));
        }

        return $this->render('competition/new.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/competition/getGeojson", name="competition_get_geojson")
     */
    public function getGeoJson(Request $request)
    {

        $competitions = $this->getDoctrine()->getRepository(Competition::class)->findAll();

        $pastCompetitions = array();
        $futureCompetitions = array();

        foreach ($competitions as $competition) {

            $description  =
                "<b>".$competition->getName()."</b><br>Du "
                .$competition->getDateStart()->format('d-m')." au "
                .$competition->getDateEnd()->format('d-m').
                "<br> <a href='".
                $this->generateUrl('competition_show', array('id'=>$competition->getId())).
                "'>Voir cette compétition</a>";

            $properties = array(
                'name' => $competition->getName(),
                'description' => $description,
            );

            $geometry = array(
                "type" => "Point",
                "coordinates" => array($competition->getLocation()->getY(),$competition->getLocation()->getX())
            );

            $feature = array(
                "type" => "Feature",
                "properties" => $properties,
                "geometry" => $geometry,
            );

            if($competition->getDateEnd() > new \DateTime())
            {
                array_push($pastCompetitions, $feature);
            }else{
                array_push($futureCompetitions, $feature);
            }
        }

        $data = array(
            "pastCompetitions" => $pastCompetitions,
            "futureCompetitions" => $futureCompetitions
        );


        return new JsonResponse($data);
    }
}