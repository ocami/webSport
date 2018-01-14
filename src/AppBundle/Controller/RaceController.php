<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Organizer;
use AppBundle\Entity\Race;
use AppBundle\Entity\Category;
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

    private function raceRepository()
    {
        $repo = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Race');

        return $repo;
    }

    private function repository($class)
    {
        $competionRepository = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:'.$class);

        return $competionRepository;
    }


    /**
     * @Route("/race/show/{id}", name="race_show")
     */
    public function showAction(Race $race)
    {

        $competitorsRace = $this->repository('RaceCompetitor')->findByRace($race);

        return $this->render('race/show.html.twig', array(
            'race' => $race,
            'competitorsRace' => $competitorsRace
        ));
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

            $competition=$this->repository('Competition')->find($idCompetition);
            $race->setCompetition($competition);

            foreach ($race->getChampionships() as $championship)
            {
                $race->addCategory($championship->getCategory());
            }

            $race=$this->get(CodeService::class)->generate($race);

            $em = $this->getDoctrine()->getManager();
            $em->persist($race);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Course bien enregistrée.');

            return $this->redirectToRoute('competition_show',array('idCompetition'=>$idCompetition));
        }

        return $this->render('race/new.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Security("has_role('ROLE_ORGANIZER')")
     * @Route("/race/new/{idCompetition}", name="race_new")
     */
    public function newAction(Request $request, $idCompetition)
    {
        $race = new Race();
        $organizer = $this->get(UserService::class)->currentUserApp(new Organizer());
        $form = $this->createForm(RaceType::class, $race, array('organizer'=>$organizer));


        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $competition=$this->repository('Competition')->find($idCompetition);

            var_dump($race);

            $race->setCompetition($competition);

            $race=$this->get(CodeService::class)->generate($race);

            $em = $this->getDoctrine()->getManager();
            $em->persist($race);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Course bien enregistrée.');

            return $this->redirectToRoute('competition_show',array('idCompetition'=>$idCompetition));
        }

        return $this->render('race/new.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Security("has_role('ROLE_ORGANIZER')")
     * @Route("/race/edit/{id}", name="race_edit")
     */
    public function editAction(Request $request, Race $race)
    {
        $race = new Race();
        $organizer = $this->get(UserService::class)->currentUserApp(new Organizer());
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