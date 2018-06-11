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
use AppBundle\Repository\CompetitorRepository;
use AppBundle\Entity\Race;
use AppBundle\Services\CodeService;
use AppBundle\Services\UserService;
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
     * @Security("has_role('ROLE_COMPETITOR')")
     * @Route("competitor/show"), name"competitor_show")
     */
    public function show(Request $request)
    {
        $competitor = $this->get(UserService::class)->getCompetitor();

        return $this->render('competitor/show.html.twig', array(
            'competitor' => $competitor,
            'user' => $this->getUser()
        ));
    }

    /**
     * @Route("/competitor/jsonByUser", options={"expose"=true}, name="competitor_json_userId")
     */
    public function getJson(Request $request)
    {
        $userId = $request->query->get('userId');
        $competitor = $this->getDoctrine()->getRepository(Competitor::class)->findOneBy(array('userId'=>$userId));
        $cData = $this->getDoctrine()->getRepository(Competitor::class)->toString($competitor);
        $cData['category'] = $this->get(UserService::class)->getCategoryCompetitor()->getName();
        $cData['age'] = $competitor->getAge();

        return new JsonResponse($cData);
    }

    /**
     * @Route("/competitor/addRace/{race}"), name"competitor_addRace")
     * @Security("has_role('ROLE_COMPETITOR')")
     */
    public function addRace(Request $request, Race $race)
    {
        $competitor = $this->get(UserService::class)->getCompetitor();
        $competitorIsRegisterToRace = $this->getDoctrine()->getRepository(RaceCompetitor::class)->competitorIsRegisterToRace($race,$competitor);

        if ($competitorIsRegisterToRace) {
            $request->getSession()->getFlashBag()->add('success', 'Vous êtes déjà inscrit à cette course');
            return $this->redirectToRoute('competitor_show');
        }

        $raceComp = new RaceCompetitor();
        $raceComp->setCompetitor($competitor);
        $raceComp->setRace($race);
        $em = $this->getDoctrine()->getManager();
        $em->persist($raceComp);
        $em->flush();
        $this->get(CodeService::class)->generateCode($raceComp);
        $request->getSession()->getFlashBag()->add('success', 'Votre inscription est enregistrée');

        return $this->redirectToRoute('competition_show', array('id'=>$race->getCompetition()->getId()));
    }

    /**
     * @Route("/competitor/removeRace/{race}"), name"competitor_removeRace")
     * @Security("has_role('ROLE_COMPETITOR')")
     */
    public function removeRace(Request $request, Race $race)
    {
        $competitor = $this->get(UserService::class)->getCompetitor();

        $em = $this->getDoctrine()->getManager();

        $rc = $em->getRepository(RaceCompetitor::class)->getRC($race,$competitor);

        if (!is_null($rc)){
            $em->remove($rc);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'Votre inscription est annulée');
        }

        return $this->redirectToRoute('competition_show', array('id'=>$race->getCompetition()->getId()));
    }
}