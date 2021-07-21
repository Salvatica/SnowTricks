<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;


class SmokeTest extends WebTestCase
{
    public function testHomePage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Snow Tricks');
    }

    public function testShowOneFigure(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/figure/montagne-3');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Montagne 3');
        $this->assertCount(10, $crawler->filter('.one-comment'));
    }

    public function testPagination(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/figure/montagne-1?offset=10');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.one-comment', 'My comment 10');
        $this->assertCount(10, $crawler->filter('.one-comment'));
    }

    public function testCreateFigure(): void
    {
        $client = static::createClient();
        //On récupère le repository et l'utilisateur de test
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('flo@flo.fr');

        //On connecte le user
        $client->loginUser($testUser);

        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('create');

        $crawler = $client->request('GET', '/figure/new');
        $this->assertResponseIsSuccessful();

    }

    public function testFigureEdit(): void
    {
        $client = static::createClient();
        //On récupère le repository et l'utilisateur de test
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('flo@flo.fr');

        //On connecte le user
        $client->loginUser($testUser);
        //on affiche le formulaire d'édition


        $crawler = $client->request('GET', '/figure/montagne-13/edit');

        $this->assertResponseIsSuccessful();

    }

    public function testFigureRemove(): void
    {

        $client = static::createClient();
        //On récupère le repository et l'utilisateur de test
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('flo@flo.fr');

        //On connecte le user
        $client->loginUser($testUser);

        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('remove');
        //on affiche le formulaire d'édition
        $crawler = $client->request('GET', '/figure/montagne-13/remove');

        $this->assertResponseStatusCodeSame(302);


    }

    public function testRegistration(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Registration on website');
    }

    public function testLogin(): void
    {
        $client = static::createClient();

        //On récupère le repository et l'utilisateur de test
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('flo@flo.fr');

        //On connecte le user
        $client->loginUser($testUser);

        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('login');

        $crawler = $client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(302);

    }

    public function testLogout(): void
    {
        $client = static::createClient();

        //On récupère le repository et l'utilisateur de test
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('flo@flo.fr');

        //On connecte le user
        $client->loginUser($testUser);

        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('logout');

        $crawler = $client->request('GET', '/logout');
        $this->assertResponseStatusCodeSame(302);

    }

    public function testForgot(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/forgotten');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'You forgot your password');
    }


}
