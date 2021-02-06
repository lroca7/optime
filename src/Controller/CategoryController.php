<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Form\CategoryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;

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

        }
        
        return $this->render('category/new.html.twig', [
            'categoryForm' => $form->createView()
        ]);

    }

    /**
     * @Route("/categories", name="list_category")
     */
    public function list(CategoryRepository $repository, Request $request, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q');
        $queryBuilder = $repository->getCategories($q);

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render('category/list.html.twig', [
            'products' => $pagination,
        ]);

    }
}
