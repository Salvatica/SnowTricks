<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\FigureType;
use App\Repository\FigureRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/figure')]
class FigureController extends AbstractController
{
    private $slugger;


    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;


    }

    #[Route('/new', name: 'figure_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('figureImages')->getData();
            if($photo){
                $fileUploader = new FileUploader($this->getParameter('kernel.project_dir') . "/public/uploads/photos");
                $fileName = $fileUploader->upload($photo);
                $figure->addFigureImage($fileName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($figure);
            $entityManager->flush();

            return $this->redirectToRoute('figure_show', ['id'=>$figure->getId()]);
        }

        return $this->render('figure/create.html.twig', [
            'formFigure' => $form->createView(),

        ]);
    }

    #[Route('/{id}', name: 'figure_show', methods: ['GET'])]
    public function show(Figure $figure): Response
    {
        return $this->render('figure/show.html.twig', [
            'figure' => $figure,
        ]);
    }

    #[Route('/{id}/edit', name: 'figure_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Figure $figure): Response
    {
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('figureImages')->getData();
            if($photo){
                $fileUploader = new FileUploader($this->getParameter('kernel.project_dir') . "/public/uploads/photos");
                $fileName = $fileUploader->upload($photo);
                $figure->addFigureImage($fileName);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('figure_show', ['id'=>$figure->getId()]);
        }

        return $this->render('figure/edit.html.twig', [
            'figure' => $figure,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'figure_delete', methods: ['POST'])]
    public function delete(Request $request, Figure $figure): Response
    {
        if ($this->isCsrfTokenValid('delete'.$figure->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($figure);
            $entityManager->flush();
        }

        return $this->redirectToRoute('homepage');
    }


}
