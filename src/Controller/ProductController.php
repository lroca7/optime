<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

// Include PhpSpreadsheet required namespaces
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Knp\Component\Pager\PaginatorInterface;

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

            // $data = $form->getData();

            // $newProduct = new Product();
            // $newProduct
            //     ->setCode($data['code']);

            // $data = $form->getData();

            // $errors = $validator->validate($newProduct);

            // if (count($errors) > 0) {
            //     /*
            //     * Uses a __toString method on the $errors variable which is a
            //     * ConstraintViolationList object. This gives us a nice string
            //     * for debugging.
            //     */
            //     $errorsString = (string) $errors;

            //     return new Response($errorsString);
            // }

            // return new Response('The author is valid! Yes!');
            
            $data = $form->getData();


            $categoryRepository = $em->getRepository(Category::class);
                    
            $category = $categoryRepository->find($data['category']);
            $newProduct = new Product();


            $newProduct
                ->setCode($data['code'])
                ->setName($data['name'])
                ->setDescription($data['description'])
                ->setBrand($data['brand'])
                ->setCategory($category)
                ->setPrice($data['price']);


           
                $em->persist($newProduct);
                $em->flush();
           
            
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
    public function list(ProductRepository $repository, Request $request, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q');
        $queryBuilder = $repository->getWithSearchQueryBuilder($q);

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render('product/list.html.twig', [
            'products' => $pagination,
        ]);

        // // $em = $this->getDoctrine()->getManager();

        // // $products = $em->getRepository(Product::class)
        // //     ->findAll();

        // // return $this->render('product/product.html.twig', [
        // //     'products' => $products
        // // ]);

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

    private function getData(): array
    {
        $em = $this->getDoctrine()->getManager();
        $list = [];
        $products = $em->getRepository(Product::class)->findAll();

        foreach ($products as $product) {
            $list[] = [
                $product->getCode(),
                $product->getName(),
                $product->getDescription(),
                $product->getBrand(),
                $product->getPrice()
            ];
        }
        return $list;
    }

    /**
     * @Route("/product/excel", name="product_excel")
     */
    public function generateExcel()
    {
        $spreadsheet = new Spreadsheet();
        
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getCell('A1')->setValue('Code');
        $sheet->getCell('B1')->setValue('Name');
        $sheet->getCell('C1')->setValue('Description');
        $sheet->getCell('D1')->setValue('Brand');
        $sheet->getCell('E1')->setValue('Price');
        
        // Increase row cursor after header write
        $sheet->fromArray($this->getData(),null, 'A2', true);

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        
        // Create a Temporary file in the system
        $fileName = 'excel.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
