<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use AppBundle\Entity\Address;
use AppBundle\Entity\Race;
use AppBundle\Entity\Category;
use AppBundle\Entity\RaceCompetitor;
use AppBundle\Form\RaceType;
use AppBundle\Services\CategoryService;
use AppBundle\Services\RaceService;
use AppBundle\Services\DbService;
use AppBundle\Services\UserService;
use AppBundle\Services\RankService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class RaceController extends Controller
{
    /**
     * @Route("/race/show/{id}", options={"expose"=true}, name="race_show")
     */
    public function show(Race $race)
    {
        $competitor = $this->get(UserService::class)->getCompetitor();

        $race = $this->get(UserService::class)->addUserDataInRace($race);
        $race = $this->get(RaceService::class)->postSelectOne($race);

        return $this->render('race/show.html.twig', array(
            'race' => $race,
            'competitor' => $competitor
        ));
    }

    /**
     * @Route("/race/new/{id}", name="race_new")
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function create(Request $request, Competition $competition)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->categoriesByGender();

        $race = new Race();
        $race->setCompetition($competition);
        $form = $this->createForm(RaceType::class, $race);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $this->get(RaceService::class)->create($race);

            $request->getSession()->getFlashBag()->add('success', 'Course bien enregistrée. Elle sera ouverte aux inscriptions après validation par l\'administrateur.');

            return $this->redirectToRoute('competition_show', array('id' => $competition->getId()));
        }

        return $this->render('race/new.html.twig', array(
            'race' => $race,
            'categories' => $categories,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/race/edit/{id}", name="race_edit")
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function edit(Request $request, Race $race)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->categoriesByGender();

        $form = $this->createForm(RaceType::class, $race);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $this->get(RaceService::class)->update($race);

            $request->getSession()->getFlashBag()->add('success', $race->getName() . ' à été modifiée');

            return $this->redirectToRoute('competition_show', array('id' => $race->getCompetition()->getId()));
        }

        return $this->render('race/new.html.twig', array(
            'update' => true,
            'race' => $race,
            'categories' => $categories,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/race/json",options={"expose"=true}, name="race_json")
     */
    public function getJson(Request $request)
    {
        $race = $request->query->get('race');
        $race = $this->getDoctrine()->getRepository(Race::class)->find($race);
        $race = $this->getDoctrine()->getRepository(Race::class)->toString($race);
        return new JsonResponse($race);
    }

    /**
     * @Route("/race/search", options={"expose"=true}, name="races_search")
     */
    public function search(Request $request)
    {
        $dataRequest = $request->query->get('dataSearch');
        $data = json_decode($dataRequest, true);

        $categories = $this->getDoctrine()->getRepository(Category::class)->categoriesByGender();
        $regions = $this->getDoctrine()->getRepository(Address::class)->departements();

        $racesId = $this->getDoctrine()->getRepository(Race::class)->search($data);

        $races = array();

        for ($i = 0; $i < count($racesId); $i++) {
            $r = $this->getDoctrine()->getRepository(Race::class)->find($racesId[$i]);
            $r = $this->get(RaceService::class)->postSelectOne($r);
            $r = $this->get(UserService::class)->addUserDataInRace($r);

            if ($data['enrol'])
                if (in_array($r->getCompetitorRegister(), $data['enrol']))
                    $races[$i] = $r;
                else
                    continue;

            $races[$i] = $r;
        }

        return $this->render('race/showSearch.html.twig', array(
            'races' => $races,
            'categories' => $categories,
            'dataSearch' => $dataRequest,
            'regions' => $regions
        ));
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @Route("/race/showRank/{id}", name="race_rank_show")
     */
    public function showRank(Race $race)
    {
        $competitor = $this->get(UserService::class)->getCompetitor();

        $race = $this->get(UserService::class)->addUserDataInRace($race);
        $race = $this->get(RaceService::class)->postSelectOne($race);

        return $this->render('race/showRank.html.twig', array(
            'race' => $race,
            'competitor' => $competitor
        ));
    }

    /**
     * @Route("/race/competitor", name="races_by_competitor")
     * @Security("has_role('ROLE_COMPETITOR')")
     */
    public function showByCompetitor()
    {
        $competitor = $this->get(UserService::class)->getCompetitor();

        $races = $this->getDoctrine()->getRepository(RaceCompetitor::class)->byCompetitor($competitor);

        return $this->render('competitor/showRaces.html.twig', array(
            'racePassed' => $races['racePassed'],
            'raceNoPassed' => $races['raceNoPassed']
        ));
    }

    /**
     * @Route("race/categoryTable", name="race_categoryTable")
     */
    public function categoryTable(Request $request)
    {
        $idRace = $request->query->get('idRace');
        $idCategory = $request->query->get('idCategory');

        $category = $this->getDoctrine()->getRepository(Category::class)->find($idCategory);
        $race = $this->getDoctrine()->getRepository(Race::class)->find($idRace);

        $data = $this->getDoctrine()->getRepository(RaceCompetitor::class)->categoriesRankToString($category, $race);

        return new JsonResponse($data);
    }

    /**
     * @Route("race/race_Table", options={"expose"=true}, name="race_Table")
     */
    public function race_Table(Request $request)
    {
        $data = array();
        $idRace = $request->query->get('idRace');
        $cat = $request->query->get('category');
        $race = $this->getDoctrine()->getRepository(Race::class)->find($idRace);
        $em = $this->getDoctrine()->getManager();

        if ($cat == 'all') {
            $competitors = $this->getDoctrine()->getRepository(RaceCompetitor::class)->allByRaceToString($race);

            for ($i = 0; $i < count($competitors); $i++) {
                $y = substr($competitors[$i]['date'], -10, 4);
                $cat = $this->get(CategoryService::class)->getCategory($y, $competitors[$i]['sexe']);

                $competitors[$i]['category'] = $cat->getName();
            }
        } else {
            $cat = $this->getDoctrine()->getRepository(Category::class)->find($cat);
            $competitors = $this->getDoctrine()->getRepository(RaceCompetitor::class)->allByRaceCategoryToString($race, $cat);

            for ($i = 0; $i < count($competitors); $i++) {
                $y = substr($competitors[$i]['date'], -10, 4);
                $cat = $this->get(CategoryService::class)->getCategory($y, $competitors[$i]['sexe']);

                $competitors[$i]['category'] = $cat->getName();
            }
        }

        $data['race_state'] = $race->getState();
        $data['inChampionship'] = $race->getInChampionship();
        $data['competitors'] = $competitors;

        return new JsonResponse($data);
    }

    /**
     * @Route("/race/simulateEnrol/{id}", name="race_simulateEnrol")
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function simulateEnrol(Request $request, Race $race)
    {
        $this->get(DbService::class)->simulateRaceEnrols($race);

        $request->getSession()->getFlashBag()->add('success', 'Inscriptions enregistrées');
        return $this->redirectToRoute('race_show', array('id' => $race->getId()));
    }

    /**
     * @Route("/race/enrolClosed/{id}", name="race_enrol_closed")
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function enrolClosed(Request $request, Race $race)
    {
        $this->get(RankService::class)->generateCompetitorsNumber($race);
        $request->getSession()->getFlashBag()->add('success', 'Inscriptions cloturées');
        $race->setState(1);
        $race->setEnrol(0);
        $em = $this->getDoctrine()->getManager();
        $em->persist($race);
        $em->flush();
        return $this->redirectToRoute('race_show', array('id' => $race->getId()));
    }

    /**
     * @Route("/race/competitorsTimes/{id}", name="race_competitorsTimes")
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function importCompetitorsTimes(Request $request, Race $race)
    {
        $this->get(RankService::class)->importCompetitorsTimes($race);
        $request->getSession()->getFlashBag()->add('success', 'Temps de course importés');
        $race->setState(2);
        $em = $this->getDoctrine()->getManager();
        $em->persist($race);
        $em->flush();

        return $this->redirectToRoute('race_show', array('id' => $race->getId()));
    }

    /**
     * @Route("race/countNotSupervised", options={"expose"=true}, name="race_countNotSupervised")
     */
    public function countNotSupervisedRaces(Request $request)
    {
        $nbNewRace = $this->getDoctrine()->getRepository(Race::class)->countNotSupervisedRaces();

        return new JsonResponse($nbNewRace);
    }
}