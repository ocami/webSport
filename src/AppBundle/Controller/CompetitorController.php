<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 30/12/2017
 * Time: 15:06
 */

namespace AppBundle\Controller;

use AppBundle\Entity\RaceCompetitor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\UserService;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Race;
use AppBundle\Form\CompetitorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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

    private function curentCompetitor()
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
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $user->addRole('ROLE_COMPETITOR');

            $competitor->setUserId($user->getId());

            $em->persist($competitor);
            $em->persist($user);
            $em->flush();

            $token = $userService->refreshToken($user);
            $security = $this->container->get('security.token_storage');
            $security->setToken($token);

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
        $competitor = $this->curentCompetitor();
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

        $raceComp = new RaceCompetitor();

        $competitor = $this->curentCompetitor();

        $competitorExist = $this->repository('RaceCompetitor')->findOneBy(array('race' => $race, 'competitor' => $competitor));

        if ($competitorExist != null) {
            $request->getSession()->getFlashBag()->add('notice', 'Vous êtes déjà inscrit à cette course');
            return $this->redirectToRoute('competitor_show');
        }

        $raceComp->setCompetitor($competitor);
        $raceComp->setRace($race);
        $request->getSession()->getFlashBag()->add('notice', 'La course à été ajoutée');
        $em = $this->getDoctrine()->getManager();
        $em->persist($raceComp);
        $em->flush();

        return $this->redirectToRoute('competitor_show');
    }
}