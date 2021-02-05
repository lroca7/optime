<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    /**
     * @Route("/category/new", name="new_category")
     */
    public function save(EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(CategoryFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form->getData());
            $data = $form->getData();

            $newCategory = new Category();

            $newCategory
                ->setName($data['name'])
                ->setActive($data['active']);

            $em->persist($newCategory);
            $em->flush();

            // return $this->redirectToRoute('categories');
        }
        
        return $this->render('category/new.html.twig', [
            'categoryForm' => $form->createView()
        ]);

        // $em = $this->getDoctrine()->getManager();

        // $newCategory = new Category();

        // $newCategory
        //     ->setName('Tomate')
        //     // ->setCreatedAt(new \DateTime())
        //     // ->setUpdatedAt(new \DateTime())
        //     ->setActive(true);

        // $em->persist($newCategory);
        // $em->flush();

        // return new Response('Saved new category with id '.$newCategory->getId());
    }

    /**
     * @Route("/categories", name="list_category")
     */
    public function list(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $categoryRepository = $em->getRepository(Category::class);

        $categories = $categoryRepository->findAll();
        $data = [];

        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'active' => $category->getActive(),
                'createdAt' => $category->getCreatedAt(),
                'updatedAt' => $category->getUpdatedAt(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }
}
