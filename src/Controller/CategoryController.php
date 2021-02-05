<?php

namespace App\Controller;

use App\Entity\Category;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
    public function save(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $newCategory = new Category();

        $newCategory
            ->setName('Tomate')
            // ->setCreatedAt(new \DateTime())
            // ->setUpdatedAt(new \DateTime())
            ->setActive(true);

        $em->persist($newCategory);
        $em->flush();

        return new Response('Saved new category with id '.$newCategory->getId());
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
