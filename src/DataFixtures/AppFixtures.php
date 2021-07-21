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
        $categoryTypes = ["Straight airs", "Grabs", "Spins", "Flips", "Inverted hand plants", "Slides", "Stalls"];
        foreach ($categoryTypes as $type) {
            $category = new Category();
            $category->setDescription($type . " description");
            $category->setTitle($type);
            $manager->persist($category);
            $categoryList[] = $category;
        }

        for ($i = 1; $i <= 20; $i++) {
            $figure = new Figure();
            $figure->setDescription("My description " . $i);
            $figure->setName("Figure " . $i);
            $figure->setCategory($categoryList[array_rand($categoryList)]);

            for ($j = 1; $j <= 20; $j++) {
                $comment = new Comment();
                $comment->setContent("My comment " . $j);
                $comment->setUser($userList[array_rand($userList)]);
                $figure->addComment($comment);
            }

            $manager->persist($figure);
        }

        $manager->flush();
    }

}
