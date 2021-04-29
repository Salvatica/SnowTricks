<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\FigureImage;
use App\Entity\FigureVideo;
use App\Form\FigureType;
use App\Repository\FigureRepository;
use App\Service\FileUploader;
use App\Service\VideoLinkSanitizer;
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
    private $videoLinkSanitizer;


    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
        $this->videoLinkSanitizer = new VideoLinkSanitizer();


    }

    #[Route('/new', name: 'figure_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $figure = new Figure();

        $form = $this->createForm(FigureType::class, $figure);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {






            $photos = $form->get('files')->getData();
            $this->handleImages($photos, $figure);
            $figureVideos = $form->get('figureVideos')->getData();
            /**
             * @Var FigureVideo $figureVideo
             */
            foreach ($figureVideos as $figureVideo){
                $url = $figureVideo->getFileName();
                $figureVideo->setFileName($this->videoLinkSanitizer->clean($url));
                $figureVideo->setFigure($figure);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($figure);
            $entityManager->flush();
            $this->addFlash("success", "L'ajout a bien été effectué");

            return $this->redirectToRoute('homepage', ['id'=>$figure->getId()]);
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


            $photos = $form->get('files')->getData();
            $this->handleImages($photos,$figure);
            $figureVideos = $form->get('figureVideos')->getData();
            /**
             * @Var FigureVideo $figureVideo
             */
            foreach ($figureVideos as $figureVideo){
                $url = $figureVideo->getFileName();
                $figureVideo->setFileName($this->videoLinkSanitizer->clean($url));
                $figureVideo->setFigure($figure);
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success", "La modification a bien été effectuée");

            return $this->redirectToRoute('homepage', ['id'=>$figure->getId()]);
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
            $this->addFlash("success", "La suppression a bien été effectuée");
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @param $photos
     * @param Figure $figure
     */
    private function handleImages($photos, $figure){
        foreach($photos as $photo){
            $fileUploader = new FileUploader($this->getParameter('kernel.project_dir') . "/public/uploads/photos");
            $fileName = $fileUploader->upload($photo);
            $figureImage = new FigureImage;
            $figureImage->setFileName($fileName);
            /**
             * @var Figure
             */
            $figure->addFigureImage($figureImage);
        }
    }

    /**
     * @param $videos
     * @param Figure $figure
     */

    private function handleVideos($videos, $figure){
        foreach($videos as $video){
            $fileUploader = new FileUploader($this->getParameter('kernel.project_dir') . "/public/uploads/photos");
            $fileName = $fileUploader->upload($video);
            $figureVideo = new FigureVideo;
            $figureVideo->setFileName($fileName);
            /**
             * @var Figure
             */
            $figure->addFigureVideo($figureVideo);
        }
    }

}
