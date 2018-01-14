<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Championship;
use AppBundle\Entity\Organizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ChampionshipType;




class ChampionshipController extends Controller
{

    private function competionRepository()
    {
        $competionRepository = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Championship');

        return $competionRepository;
    }

    private function repository($class)
    {
        $competionRepository = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:'.$class);

        return $competionRepository;
    }


    /**
     * @Route("/championship/show/{id}", name="championship_show")
     */
    public function showAction(Championship $championship)
    {
        return $this->render('championship/show.html.twig', array('championship' => $championship));
    }

    /**
     * @Route("/championship/show_all", name="championship_show_all")
     */
    public function showAllAction()
    {
        $championships = $this->competionRepository()->findAll();

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


            $em = $this->getDoctrine()->getManager();
            $em->persist($championship);
            $em->flush();

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
            $em = $this->getDoctrine()->getManager();
            $em->persist($championship);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Course bien enregistrée.');

            return $this->redirectToRoute('championship/show.html.twig', array('id'=>$championship->getId()));
        }

        return $this->render('championship/new.html.twig', array('form' => $form->createView()));
    }
}