<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoriesFixtures extends Fixture
{
    private $counter = 0;

    public function load(ObjectManager $manager): void
    {
        $this->createCategory('Sport', $manager);
        $this->createCategory('Musique', $manager);
    }

    public function createCategory(string $name, ObjectManager $manager): Categories
    {
        $category= new Categories();
        $category->setName($name);
        $manager->persist($category);
        $manager->flush();

        $this->addReference('cat-'.$this->counter, $category);
        $this->counter++;
        

        return $category;
    }
}
