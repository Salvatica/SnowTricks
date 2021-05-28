<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
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
            $figure->setDescription("Ma description ".$i) ;
            $figure->setName("Montagne ".$i) ;
            $figure->setCategory($category);

            $manager->persist($figure);

        }

        for($i = 0; $i < 10 ; $i++){
            $comment = new Comment();
            $comment->setContent("Mon commentaire ".$i) ;
            $comment->setFigure($figure);

            $manager->persist($comment);

        }

        $manager->flush();
    }
}
