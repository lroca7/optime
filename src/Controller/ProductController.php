<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    /**
     * @Route("/product/new", name="new_product")
     */
    public function save(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $categoryRepository = $em->getRepository(Category::class);
        
        $category = $categoryRepository->find(1);

        $newProduct = new Product();

        $newProduct
            ->setCode('1')
            ->setName('Dolex')
            ->setDescription('Antinflamatorio')
            ->setBrand('Genfar')
            ->setCategory($category)
            ->setPrice(12000);

        $em->persist($newProduct);
        $em->flush();

        return new Response('Saved new product with id '.$newProduct->getId());
    }

    /**
     * @Route("/products", name="list_products")
     */
    public function list(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository(Product::class);

        $products = $productRepository->findAll();
        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'createdAt' => $product->getCreatedAt(),
                'updatedAt' => $product->getUpdatedAt(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'brand' => $product->getBrand(),
                'category' => [
                    'id' => $product->getCategory()->getId(),
                    'name' => $product->getCategory()->getName(),
                ],
                'price' => $product->getPrice()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }
}
