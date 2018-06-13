<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Championship;
use AppBundle\Entity\ChampionshipCompetitor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class ChampionshipController extends Controller
{
    /**
     * @Route("/championship/show/{id}", name="championship_show")
     */
    public function showAction(Championship $championship)
    {
        $championships = $this->getDoctrine()->getRepository(Championship::class)->findAll();
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('championship/show.html.twig', array(
            'categories' => $categories,
            'championship' => $championship,
            'championships' => $championships
        ));
    }

    /**
     * @Route("championship/championship_json", options={"expose"=true}, name="championship_json")
     */
    public function race_Table(Request $request)
    {
        $idCategory = $request->query->get('idCategory');
        $championship = $this->getDoctrine()->getRepository(Championship::class)->findOneByCategory($idCategory);
        $data = $this->getDoctrine()->getRepository(ChampionshipCompetitor::class)->competitorsOrderByPointsToString($championship);

        return new JsonResponse($data);
    }
}