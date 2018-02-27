<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Championship;
use AppBundle\Entity\Competition;
use AppBundle\Services\EntityService;
use AppBundle\Services\RanckService;
use Proxies\__CG__\AppBundle\Entity\ChampionshipCompetitor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ChampionshipType;
use Symfony\Component\HttpFoundation\JsonResponse;


class ChampionshipController extends Controller
{
    /**
     * @Route("/championship/show/{id}", name="championship_show")
     */
    public function showAction(Championship $championship)
    {
        $championships = $this->getDoctrine()->getRepository(Championship::class)->findAll();

        return $this->render('championship/show.html.twig', array(
            'championship' => $championship,
            'championships' => $championships
        ));
    }

    /**
     * @Route("/championship/show_all", name="championship_show_all")
     */
    public function showAllAction()
    {
        $cr = $this->getDoctrine()->getRepository(Competition::class);
        $championships = $cr->findAll();

        return $this->render('championship/showList.html.twig', array('championships' => $championships));
    }

    /**
     * @Route("/championship/new", name="championship_new")
     */
    public function newAction(Request $request)
    {
        $championship = new Championship();
        $form = $this->createForm(ChampionshipType::class, $championship);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $this->get(EntityService::class)->create($championship);
            $this->get(EntityService::class)->create($championship->getCategory());

            $request->getSession()->getFlashBag()->add('notice', 'Championnat bien enregistrée.');
            return $this->redirectToRoute('index');
        }
        return $this->render('championship/new.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/championship/edit/{id}", name="championship_edit")
     */
    public function editAction(Request $request, Championship $championship)
    {
        $form = $this->createForm(ChampionshipType::class, $championship);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->get(EntityService::class)->update($championship);
            $this->get(EntityService::class)->update($championship->getCategory());

            $request->getSession()->getFlashBag()->add('notice', 'Championat bien enregistré.');
            return $this->redirectToRoute('admin_index');
        }
        return $this->render('championship/new.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("championship/championship_json", name="championship_json")
     */
    public function race_Table(Request $request)
    {
        $idCategory = $request->query->get('idCategory');
        $championship = $this->getDoctrine()->getRepository(Championship::class)->findOneByCategory($idCategory);
        $data = $this->getDoctrine()->getRepository(ChampionshipCompetitor::class)->competitorsOrderByPointsToString($championship);

        return new JsonResponse($data);
    }

    /**
     * @Route("championship/getAllJson", name="championship_getAllJson")
     */
    public function getAllJson(Request $request)
    {
        $categories = $this->getDoctrine()->getRepository(Championship::class)->toString();

        for ($i=0;$i < count($categories); $i++)
        {
            $id = $categories[$i]['id'];
            $path = $this->redirectToRoute('championship_show', array('id'=>$id));
            $categories[$i]['path'] = $path->getTargetUrl();
        }

        return new JsonResponse($categories);
    }

}