<?php

namespace App\DataFixtures;

use App\Entity\Category as Category;
use App\Entity\Product;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
       
        $this->loadCategories($manager);
        $this->loadProducts($manager);

        
    }

    public function loadCategories($manager)
    {
        $newCategory = new Category();
        $newCategory
            ->setName('Bebidas')
            ->setActive(true);

        $manager->persist($newCategory);

        $newCategory = new Category();
        $newCategory
            ->setName('Alimentos')
            ->setActive(true);

        $manager->persist($newCategory);

        $manager->flush();
    }

    public function loadProducts($manager)
    {
        $repoCategory = $manager->getRepository(Category::class);
        $category = $repoCategory->findOneBy(array('name'=>'Bebidas'));

        
        $newProduct = new Product();
        $newProduct
            ->setCode('1')
            ->setName('CocaCola')
            ->setDescription('Bebida gaseosa')
            ->setBrand('Cocacola Company')
            ->setCategory($category)
            ->setPrice(5000);

        $manager->persist($newProduct);
        $manager->flush();
    }
}
