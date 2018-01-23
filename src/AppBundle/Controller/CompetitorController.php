<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 30/12/2017
 * Time: 15:06
 */

namespace AppBundle\Controller;

use AppBundle\Entity\RaceCompetitor;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Race;
use AppBundle\Services\CodeService;
use AppBundle\Services\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\CompetitorType;


class CompetitorController extends Controller
{

    protected $container;

    private function competitorRepository()
    {
        $competitorRepository = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Competitor');

        return $competitorRepository;
    }

    private function repository($class)
    {
        $repository = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:' . $class);

        return $repository;
    }

    private function currentCompetitor()
    {
        return $this->competitorRepository()->findOneByUserId($this->getUser());
    }


    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/competitor/register"), name"competitor_register")
     */
    public function registerAction(Request $request, UserService $userService)
    {
        $competitor = new Competitor();
        $form = $this->createForm(CompetitorType::class, $competitor);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $this->get(UserService::class)->registerUserApp($competitor);

            $request->getSession()->getFlashBag()->add('notice', 'Votre compte competiteur est bien enregistré');

            return $this->redirectToRoute('index');
        }

        return $this->render('competitor/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_COMPETITOR')")
     * @Route("/competitor/show", name="competitor_show")
     */
    public function showAction()
    {
        $competitor = $this->currentCompetitor();
        $races = $this->repository('RaceCompetitor')->findByCompetitor($competitor);

        return $this->render('competitor/show.html.twig', array(
            'competitor' => $competitor,
            'races' => $races
        ));
    }

    /**
     * @Security("has_role('ROLE_COMPETITOR')")
     * @Route("/competitor/addRace/{race}"), name"competitor_addRace")
     */
    public function addRaceAction(Request $request, Race $race)
    {
        $competitor = $this->get(UserService::class)->currentUserApp(Competitor::class);
        $competitorIsRegisterToRace = $this->getDoctrine()->getRepository(RaceCompetitor::class)->competitorIsRegisterToRace($race,$competitor);

        if ($competitorIsRegisterToRace) {
            $request->getSession()->getFlashBag()->add('notice', 'Vous êtes déjà inscrit à cette course');
            return $this->redirectToRoute('competitor_show');
        }

        $raceComp = new RaceCompetitor();
        $raceComp->setCompetitor($competitor);
        $raceComp->setRace($race);
        $em = $this->getDoctrine()->getManager();
        $em->persist($raceComp);
        $em->flush();
        $this->get(CodeService::class)->generateCode($raceComp);
        $request->getSession()->getFlashBag()->add('notice', 'Votre inscription est enregistrée');

        return $this->redirectToRoute('competitor_show');
    }
}