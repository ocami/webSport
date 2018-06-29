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
use AppBundle\Entity\ChampionshipCompetitor;
use AppBundle\Services\CodeService;
use AppBundle\Services\UserService;
use AppBundle\Services\CompetitorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\CompetitorType;


class CompetitorController extends Controller
{
    /**
     * @Route("/competitor/register"), name"competitor_register")
     * @Security("has_role('ROLE_USER')")
     */
    public function registerAction(Request $request, UserService $userService)
    {
        $competitor = new Competitor();
        $competitor->setLevel(3);// !!! FOR DEMONSTRATION !!!! //
        $form = $this->createForm(CompetitorType::class, $competitor);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $this->get(UserService::class)->registerUserApp($competitor);
            $request->getSession()->getFlashBag()->add('success', 'Votre compte competiteur est bien enregistré');
            return $this->redirectToRoute('index');
        }

        return $this->render('competitor/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("competitor/show/{id}", options={"expose"=true}, name="competitor_show")
     */
    public function show(Competitor $competitor)
    {
        $competitor = $this->get(CompetitorService::class)->setCategoryCompetitor($competitor);

        return $this->showCompetitor($competitor);
    }

    /**
     * @Route("competitor/show_current", options={"expose"=true}, name="competitor_show_current")
     */
    public function showCurrent()
    {
        $competitor = $this->get(UserService::class)->getCompetitor();

        return $this->showCompetitor($competitor);
    }


    private function showCompetitor(Competitor $competitor)
    {
        $rc = $this->getDoctrine()->getRepository(RaceCompetitor::class)->findBy(array('competitor' => $competitor));
        $cc = $this->getDoctrine()->getRepository(ChampionshipCompetitor::class)->findOneBy(array('competitor' => $competitor));
        $racesStat = $this->getDoctrine()->getRepository(Competitor::class)->racesStat($competitor);

        return $this->render('competitor/show.html.twig', array(
            'competitor' => $competitor,
            'rc' => $rc,
            'cc' => $cc,
            'racesStat' => $racesStat
        ));
    }

    /**
     * @Route("/competitor/jsonByUser", options={"expose"=true}, name="competitor_json_userId")
     */
    public function getJson(Request $request)
    {
        $userId = $request->query->get('userId');
        $competitor = $this->getDoctrine()->getRepository(Competitor::class)->findOneBy(array('userId' => $userId));
        $cData = $this->getDoctrine()->getRepository(Competitor::class)->toString($competitor);
        $cData['category'] = $this->get(UserService::class)->getCategoryCompetitor()->getName();
        $cData['age'] = $competitor->getAge();

        return new JsonResponse($cData);
    }

    /**
     * @Route("/competitor/add_race/{race}"), name"competitor_add_race")
     * @Security("has_role('ROLE_COMPETITOR')")
     */
    public function addRace(Request $request, Race $race)
    {
        $competitor = $this->get(UserService::class)->getCompetitor();
        $competitorIsRegisterToRace = $this->getDoctrine()->getRepository(RaceCompetitor::class)->competitorIsRegisterToRace($race, $competitor);

        if ($competitorIsRegisterToRace) {
            $request->getSession()->getFlashBag()->add('success', 'Vous êtes déjà inscrit à cette course');
            return $this->redirectToRoute('competitor_show');
        }

        $raceComp = new RaceCompetitor();
        $raceComp->setCompetitor($competitor);
        $raceComp->setRace($race);
        $raceComp = $this->get(CodeService::class)->generateCode($raceComp);
        $em = $this->getDoctrine()->getManager();
        $em->persist($raceComp);
        $em->flush();
        $request->getSession()->getFlashBag()->add('success', 'Votre inscription est enregistrée');

        return $this->redirectToRoute('race_show', array('id' => $race->getId()));
    }

    /**
     * @Route("/competitor/remove_race/{race}"), name"competitor_remove_race")
     * @Security("has_role('ROLE_COMPETITOR')")
     */
    public function removeRace(Request $request, Race $race)
    {
        $competitor = $this->get(UserService::class)->getCompetitor();

        $em = $this->getDoctrine()->getManager();

        $rc = $em->getRepository(RaceCompetitor::class)->getRC($race, $competitor);

        if (!is_null($rc)) {
            $em->remove($rc);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'Votre inscription est annulée');
        }

        return $this->redirectToRoute('race_show', array('id' => $race->getId()));
    }

    /**
     * @Route("/competitor/next_race", options={"expose"=true}, name="competitor_next_race")
     */
    public function nextRace(Request $request)
    {
        $competitor = $this->get(UserService::class)->getCompetitor()->getId();

        $nextRace = $this->getDoctrine()->getRepository(Competitor::class)->nextRace($competitor);


        return new JsonResponse($nextRace);
    }

}