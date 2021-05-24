<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\FigureImage;
use App\Entity\FigureVideo;
use App\Form\FigureType;
use App\Service\FileUploader;
use App\Service\VideoLinkSanitizer;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/figure')]
class FigureController extends AbstractController
{
    public function __construct(private VideoLinkSanitizer $videoLinkSanitizer)
    {
    }

    #[Route('/new', name: 'figure_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleImages($form->get('files')->getData(), $figure);
            $entityManager->persist($figure);
            $entityManager->flush();
            $this->addFlash("success", "L'ajout a bien été effectué");

            return $this->redirectToRoute('homepage', ['id' => $figure->getId()]);
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
            $this->handleImages($photos, $figure);
            $figureVideos = $form->get('figureVideos')->getData();
            /**
             * @Var FigureVideo $figureVideo
             */
            foreach ($figureVideos as $figureVideo) {
                $url = $figureVideo->getFileName();
                $figureVideo->setFileName($this->videoLinkSanitizer->clean($url));
                $figureVideo->setFigure($figure);
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success", "La modification a bien été effectuée");

            return $this->redirectToRoute('figure_edit', ['id' => $figure->getId()]);
        }

        return $this->render('figure/edit.html.twig', [
            'figure' => $figure,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/remove', name: 'figure_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Figure $figure): Response
    {
        if ($this->isCsrfTokenValid('delete' . $figure->getId(), $request->query->get('_token'))) {
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
    private function handleImages($photos, $figure)
    {
        foreach ($photos as $photo) {
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
     * @param Request $request
     * @param Figure $figure
     * @param FigureImage $figureImage
     * @return Response
     *
     * @ParamConverter("figure", options={"mapping": {"figureId": "id"}})
     * @ParamConverter("figureImage", options={"mapping": {"imageId": "id"}})
     */
    #[Route('/{figureId}/removeFigureImage/{imageId}', name: 'image_delete', methods: ['GET'])]
    public function deleteImage(Request $request, Figure $figure, FigureImage $figureImage): Response
    {
            $entityManager = $this->getDoctrine()->getManager();
            dump($figureImage);
            $figure->removeFigureImage($figureImage);
            $entityManager->persist($figure);
            $entityManager->flush();

        return $this->redirectToRoute('figure_edit', array('id' => $figure->getId()) );
    }


    /**
     * @param $videos
     * @param Figure $figure
     */
    private function handleVideos($videos, $figure)
    {
        foreach ($videos as $video) {
            $figure->addFigureVideo($video);
        }
    }

    /**
     * @param Request $request
     * @param Figure $figure
     * @param FigureVideo $figureVideo
     * @return Response
     *
     * @ParamConverter("figure", options={"mapping": {"figureId": "id"}})
     * @ParamConverter("figureVideo", options={"mapping": {"videoId": "id"}})
     */
    #[Route('/{figureId}/removeFigureVideo/{videoId}', name: 'video_delete', methods: ['GET'])]
    public function deleteVideo(Request $request, Figure $figure, FigureVideo $figureVideo): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        dump($figureVideo);
        $figure->removeFigureVideo($figureVideo);
        $entityManager->persist($figure);
        $entityManager->flush();


        return $this->redirectToRoute('figure_edit', array('id' => $figure->getId()) );
    }
}
