<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Figure;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        for($i = 0; $i < 3 ; $i++){
            $category = new Category();
            $category->setDescription("Ma  description ".$i) ;
            $category->setTitle("Title".$i) ;
            $manager->persist($category);
        }


        for($i = 0; $i < 10 ; $i++){
            $figure = new Figure();
            $figure->setDescription("Ma descriotion ".$i) ;
            $figure->setName("Montagne ".$i) ;
            $figure->setCategory($category);

            $manager->persist($figure);

        }

        $manager->flush();
    }
}
