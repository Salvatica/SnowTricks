<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\FigureType;
use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage', methods: ['GET'])]
    public function index(FigureRepository $figureRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'figures' => $figureRepository->findAll(),
        ]);
    }


}
