<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Organizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\CategoryType;




class CategoryController extends Controller
{

    private function competionRepository()
    {
        $competionRepository = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Category');

        return $competionRepository;
    }

    private function repository($class)
    {
        $competionRepository = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:'.$class);

        return $competionRepository;
    }

    /**
     * @Route("/category/show/{id}", name="category_show")
     */
    public function showAction(Category $category)
    {
        return $this->render('category/show.html.twig', array('category' => $category));
    }

    /**
     * @Route("/category/show_all", name="category_show_all")
     */
    public function showAllAction()
    {
        $categorys = $this->repo()->findAll();

        return $this->render('category/showList.html.twig', array('categorys' => $categorys));
    }

    /**
     * @Route("/category/new/{idCompetition}", name="category_new")
     */
    public function newAction(Request $request, $idCompetition)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);


        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $organizer = $this->repository('Organizer')->findOneByUserId($this->getUser());

            $category->setCreateBy($organizer->getId());

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Catégorie bien enregistrée.');

            return $this->redirectToRoute('race_competition_show',array('idCompetition'=>$idCompetition));
        }

        return $this->render('category/new.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/category/edit/{id}", name="category_edit")
     */
    public function editAction(Request $request, Category $category)
    {
        $form = $this->createForm(CategoryType::class, $category);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Course bien enregistrée.');

            return $this->redirectToRoute('category/show.html.twig',array('category'=>$category));
        }

        return $this->render('category/new.html.twig', array('form' => $form->createView()));
    }

}