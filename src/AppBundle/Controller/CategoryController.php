<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Competition;
use AppBundle\Entity\Organizer;
use AppBundle\Services\CodeService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\CategoryType;
use AppBundle\Services\UserService;

class CategoryController extends Controller
{
    /**
     * @Route("/category/show/{id}", name="category_show")
     */
    public function showAction(Category $category)
    {
        return $this->render('category/show.html.twig', array('category' => $category->getId()));
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
     * @Route("/category/show_byOrganizer", name="category_show_byOrganizer")
     */
    public function showByOrganizer()
    {
        $organizer = $this->get(UserService::class)->currentUserApp(Organizer::class);
        $categories = $this->getDoctrine()->getRepository(Category::class)->findByCreateBy($organizer);

        return $this->render('category/showList.html.twig', array('categories' => $categories));
    }

    /**
     * @Route("/category/new/{id}", name="category_new")
     */
    public function newAction(Request $request, Competition $competition)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $organizer = $this->getDoctrine()->getRepository(Organizer::class)->findOneByUserId($this->getUser());
            $category->setCreateBy($organizer->getId());
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            $this->get(CodeService::class)->generateCode($category);

            $request->getSession()->getFlashBag()->add('notice', 'Catégorie bien enregistrée.');

            return $this->redirectToRoute('competition_show',array('id'=>$competition->getId()));
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

            return $this->redirectToRoute('category/show.html.twig',array('category'=>$category->getId()));
        }

        return $this->render('category/new.html.twig', array('form' => $form->createView()));
    }

}