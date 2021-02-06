<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function new(EntityManagerInterface $em, Request $request, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(ProductFormType::class);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $newProduct = new Product();
            $newProduct
                ->setCode($data['code']);

            $data = $form->getData();

            $errors = $validator->validate($newProduct);

            if (count($errors) > 0) {
                /*
                * Uses a __toString method on the $errors variable which is a
                * ConstraintViolationList object. This gives us a nice string
                * for debugging.
                */
                $errorsString = (string) $errors;

                return new Response($errorsString);
            }

            return new Response('The author is valid! Yes!');
            
            // dd($form->getData());
            // $data = $form->getData();

            // print_r($data);

            // $categoryRepository = $em->getRepository(Category::class);
                    
            // $category = $categoryRepository->find($data['category']);
            // $newProduct = new Product();


            // $newProduct
            //     ->setCode($data['code'])
            //     ->setName($data['name'])
            //     ->setDescription($data['description'])
            //     ->setBrand($data['brand'])
            //     ->setCategory($category)
            //     ->setPrice($data['price']);

            // $errors = $validator->validate($newProduct);

            // if (count($errors) > 0) {
            //     /*
            //     * Uses a __toString method on the $errors variable which is a
            //     * ConstraintViolationList object. This gives us a nice string
            //     * for debugging.
            //     */
            //     $errorsString = (string) $errors;

            //     return new Response($errorsString);
            // }else {
            //     $em->persist($newProduct);
            //     $em->flush();
            // }

            
        }
        
        return $this->render('product/new.html.twig', [
            'productForm' => $form->createView()
        ]);

        // $em = $this->getDoctrine()->getManager();

        // $categoryRepository = $em->getRepository(Category::class);
        
        // $category = $categoryRepository->find(1);

        // $newProduct = new Product();

        // $newProduct
        //     ->setCode('1')
        //     ->setName('Dolex')
        //     ->setDescription('Antinflamatorio')
        //     ->setBrand('Genfar')
        //     ->setCategory($category)
        //     ->setPrice(12000);

        // $em->persist($newProduct);
        // $em->flush();

        // return new Response('Saved new product with id '.$newProduct->getId());
    }

    /**
     * @Route("/products", name="list_products")
     */
    public function list(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository(Product::class)
            ->findAll();

        return $this->render('product/list.html.twig', [
            'products' => $products
        ]);

        // $em = $this->getDoctrine()->getManager();
        // $productRepository = $em->getRepository(Product::class);

        // $products = $productRepository->findAll();
        // $data = [];

        // foreach ($products as $product) {
        //     $data[] = [
        //         'id' => $product->getId(),
        //         'createdAt' => $product->getCreatedAt(),
        //         'updatedAt' => $product->getUpdatedAt(),
        //         'name' => $product->getName(),
        //         'description' => $product->getDescription(),
        //         'brand' => $product->getBrand(),
        //         'category' => [
        //             'id' => $product->getCategory()->getId(),
        //             'name' => $product->getCategory()->getName(),
        //         ],
        //         'price' => $product->getPrice()
        //     ];
        // }

        // return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/product/{id}/edit", name="product_edit")
     */
    public function edit(EntityManagerInterface $em, Product $product, Request $request)
    {
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();
            // $this->addFlash('success', 'Product Updated! Inaccuracies squashed!');
            
        }
        return $this->render('product/edit.html.twig', [
            'productForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/product/{id}/delete", name="product_delete")
     */
    public function delete($id, EntityManagerInterface $em, Request $request)
    {
       
        $product = $em->getRepository(Product::class)->find($id);

        if($product) {
            $em->remove($product);
            $em->flush();

            return new JsonResponse(['msj' => 'Product deleted'], Response::HTTP_OK);
        }
    }
}
