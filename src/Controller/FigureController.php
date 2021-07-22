<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\FigureImage;
use App\Entity\FigureVideo;
use App\Form\CommentType;
use App\Form\FigurePhotoType;
use App\Form\FigureType;
use App\Form\FigureVideoType;
use App\Manager\FigureManager;
use App\Repository\CommentRepository;
use App\Service\FileUploader;
use App\Service\VideoLinkSanitizer;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/figure')]
class FigureController extends AbstractController
{

    public function __construct(private VideoLinkSanitizer $videoLinkSanitizer, private FigureManager $figureManager)
    {
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    #[Route('/new', name: 'figure_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->figureManager->handleImages($form->get('files')->getData(), $figure);
            $this->figureManager->saveFigure($figure);
            $this->addFlash("success", "The addition has been made");

            return $this->redirectToRoute('figure_edit', ['slug' => $figure->getSlug()]);
        }

        return $this->render('figure/create.html.twig', [
            'formFigure' => $form->createView(),

        ]);
    }

    #[Route('/{slug}', name: 'figure_show', methods: ['GET', 'POST'])]
    public function show(Request $request, EntityManagerInterface $entityManager, Figure $figure, CommentRepository $commentRepository): Response // rajout du comment
    {
        $offset = max(0, $request->query->getInt('offset', 0));// pagination comments
        $paginator = $commentRepository->getCommentPaginator($figure, $offset);

        $form = $this->createForm(CommentType::class);
        if($this->getUser()){
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $comment = $form->getData();
                $this->figureManager->addComment($figure,$this->getUser(),$comment);
                return $this->redirectToRoute('figure_show', ['slug' => $figure->getSlug()]);
            }
        }

        return $this->render('figure/show.html.twig', [
            'figure' => $figure,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'commentForm' => $form->createView()
        ]);
    }

    /**
    * @IsGranted("ROLE_USER")
    */
    #[Route('/{slug}/edit', name: 'figure_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Figure $figure): Response
    {
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photos = $form->get('files')->getData();
            $this->figureManager->handleImages($photos, $figure);
            $figureVideos = $form->get('figureVideos')->getData();
            /**
             * @Var FigureVideo $figureVideo
             */

            $this->figureManager->handleVideos($figureVideos, $figure);

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success", "The modification has been made");

            return $this->redirectToRoute('figure_edit', ['slug' => $figure->getSlug()]);
        }

        return $this->render('figure/edit.html.twig', [
            'figure' => $figure,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param FigureImage $figureImage
     * @param FileUploader $fileUploader
     * @return Response
     * @ParamConverter("figureImage", options={"mapping": {"imageId": "id"}})
     * @IsGranted("ROLE_USER")
     */
    #[Route('/editOnePhoto/{imageId}', name: 'figure_editOnePhoto', methods: ['GET', 'POST'])]
    public function editOnePhoto(Request $request, FigureImage $figureImage, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(FigurePhotoType::class, $figureImage);
        $form->handleRequest($request);
        $figure = $figureImage->getFigure();

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('file')->getData();
            $this->figureManager->savePhoto($figure, $figureImage, $photo);
            $this->addFlash("success", "The modification has been made");

            return $this->redirectToRoute('figure_edit', ['id' => $figure->getId()]);
        }

        return $this->render('figure/editOnePhoto.html.twig', [
            'figure' => $figure,
            'photo' => $figureImage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @ParamConverter("figureVideo", options={"mapping": {"videoId":"id"}})
     * @IsGranted("ROLE_USER")
     */
    #[Route('/editOneVideo/{videoId}', name: 'figure_editOneVideo', methods: ['GET', 'POST'])]
    public function editOneVideo(Request $request, FigureVideo $figureVideo): Response
    {
        $figure = $figureVideo->getFigure();
        $form = $this->createForm(FigureVideoType::class, $figureVideo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success", "The modification has been made");

            return $this->redirectToRoute('figure_edit', ['slug' => $figure->getSlug()]);
        }

        return $this->render('figure/editOneVideo.html.twig', [
            'figureVideo' => $figureVideo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Figure $figure
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    #[Route('/{slug}/remove', name: 'figure_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Figure $figure): Response
    {
        if ($this->isCsrfTokenValid('delete' . $figure->getSlug(), $request->query->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($figure);
            $entityManager->flush();
            $this->addFlash("success", "The deletion was successful");
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @param Request $request
     * @param Figure $figure
     * @param FigureImage $figureImage
     * @return Response
     * @IsGranted("ROLE_USER")
     * @ParamConverter("figure", options={"mapping": {"figureId": "id"}})
     * @ParamConverter("figureImage", options={"mapping": {"imageId": "id"}})
     */
    #[Route('/{figureId}/removeFigureImage/{imageId}', name: 'image_delete', methods: ['GET'])]
    public function deleteImage(Request $request, Figure $figure, FigureImage $figureImage): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $figure->removeFigureImage($figureImage);
        $entityManager->persist($figure);
        $entityManager->flush();

        return $this->redirectToRoute('figure_edit', array('slug' => $figure->getSlug()));
    }

    /**
     * @param Request $request
     * @param Figure $figure
     * @param FigureVideo $figureVideo
     * @return Response
     * @IsGranted("ROLE_USER")
     * @ParamConverter("figure", options={"mapping": {"figureId": "id"}})
     * @ParamConverter("figureVideo", options={"mapping": {"videoId": "id"}})
     */
    #[Route('/{figureId}/removeFigureVideo/{videoId}', name: 'video_delete', methods: ['GET'])]
    public function deleteVideo(Request $request, Figure $figure, FigureVideo $figureVideo): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $figure->removeFigureVideo($figureVideo);
        $entityManager->persist($figure);
        $entityManager->flush();


        return $this->redirectToRoute('figure_edit', array('slug' => $figure->getSlug()));
    }

    /**
     * @param Request $request
     * @param Figure $figure
     * @param Comment $comment
     * @return Response
     * @ParamConverter("figure", options={"mapping": {"figureId": "id"}})
     * @ParamConverter("comment", options={"mapping": {"commentId": "id"}})
     * @IsGranted("comment_delete", subject="comment")
     */
    #[Route('/{figureId}/commentRemove/{commentId}', name: 'comment_delete', methods: ['GET', 'POST'])]
    public function removeComment(Request $request, Figure $figure, Comment $comment): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $figure->removeComment($comment);
        $entityManager->flush();
        $this->addFlash("success", "The deletion was successful");


        return $this->redirectToRoute('figure_show', ['slug' => $figure->getSlug()]);
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
}
