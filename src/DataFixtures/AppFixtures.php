<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        //Creation du compte admin
        $admin = new User();
        $admin->setName("Flo");
        $admin->setEmail("flo@flo.fr");
        $hash = $this->encoder->encodePassword($admin, "12345678");
        $admin->addRole("ROLE_ADMIN");
        $admin->setPassword($hash);
        $manager->persist($admin);

        //Création des utilisateurs

        $userList = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setName("user0" . $i);
            $user->setEmail("null" . $i);
            $hash = $this->encoder->encodePassword($user, "password");
            $userList[] = $user;
            $user->setPassword($hash);


            $manager->persist($user);

        }


        $categoryList = [];
        for ($i = 0; $i < 3; $i++) {
            $category = new Category();
            $category->setDescription("Ma  description " . $i);
            $category->setTitle("Title" . $i);
            $manager->persist($category);
            $categoryList[] = $category;
        }


        for ($i = 1; $i <= 20; $i++) {
            $figure = new Figure();
            $figure->setDescription("Ma description " . $i);
            $figure->setName("Montagne " . $i);
            $figure->setCategory($categoryList[array_rand($categoryList)]);

            for ($j = 1; $j <= 10; $j++) {
                $comment = new Comment();
                $comment->setContent("Mon commentaire " . $j);
                $comment->setUser($userList[array_rand($userList)]);
                $figure->addComment($comment);
            }

            $manager->persist($figure);
        }


        $manager->flush();
    }


}
