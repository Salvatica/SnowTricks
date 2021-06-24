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
use Twig\Environment;


#[Route('/figure')]
class FigureController extends AbstractController
{
    public function __construct(private VideoLinkSanitizer $videoLinkSanitizer)
    {
    }

// création d'une nouvelle figure
    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @IsGranted("ROLE_USER")
     */
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

            return $this->redirectToRoute('figure_edit', ['id' => $figure->getId()]);
        }

        return $this->render('figure/create.html.twig', [
            'formFigure' => $form->createView(),

        ]);
    }
// affichage d'une figure en utilisant "slug" pour l'url

    #[Route('/{slug}', name: 'figure_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Environment $twig, EntityManagerInterface $entityManager, Figure $figure, CommentRepository $commentRepository): Response // rajout du comment
    {
        $offset = max(0, $request->query->getInt('offset', 0));// pagination comments
        $paginator = $commentRepository->getCommentPaginator($figure, $offset);

        $comment = new Comment();
        $user = $this->getUser();
        $comment->setUser($user);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setFigure($figure);
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('figure_show', ['slug' => $figure->getSlug()]);
        }
        return new Response($twig->render('figure/show.html.twig', [
            'figure' => $figure,
            'comments' => $paginator, // rajout
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE, // rajout
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE), // rajout
            'commentForm' => $form->createView()
        ]));
    }
//edition d'une figure

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

// Edition d'une photo
    /**
     * @param Request $request
     * @param FigureImage $figureImage
     * @return Response
     * @ParamConverter("figureImage", options={"mapping": {"imageId": "id"}})
     */
    #[Route('/editOnePhoto/{imageId}', name: 'figure_editOnePhoto', methods: ['GET', 'POST'])]
    public function editOnePhoto(Request $request, FigureImage $figureImage): Response
    {
        $form = $this->createForm(FigurePhotoType::class, $figureImage);
        $form->handleRequest($request);
        $figure = $figureImage->getFigure();

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('file')->getData();
            $fileUploader = new FileUploader($this->getParameter('kernel.project_dir') . "/public/uploads/photos");
            $fileName = $fileUploader->upload($photo);
            $figureImage->setFileName($fileName);
            $figureImage->setFigure($figure);

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success", "La modification a bien été effectuée");

            return $this->redirectToRoute('figure_edit', ['id' => $figure->getId()]);
        }

        return $this->render('figure/editOnePhoto.html.twig', [
            'figure' => $figure,
            'photo' => $figureImage,
            'form' => $form->createView(),
        ]);
    }
// Edition d'une vidéo
    /**
     * @ParamConverter("figureVideo", options={"mapping": {"videoId":"id"}})
     */
    #[Route('/editOneVideo/{videoId}', name: 'figure_editOneVideo', methods: ['GET', 'POST'])]
    public function editOneVideo(Request $request, FigureVideo $figureVideo): Response
    {
        $figure = $figureVideo->getFigure();
        $form = $this->createForm(FigureVideoType::class, $figureVideo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success", "La modification a bien été effectuée");

            return $this->redirectToRoute('figure_edit', ['id' => $figure->getId()]);
        }

        return $this->render('figure/editOneVideo.html.twig', [
            'figure' => $figureVideo,
            'form' => $form->createView(),
        ]);
    }

// suppression d'une figure
    #[Route('/{id}/remove', name: 'figure_delete', methods: ['GET', 'POST'])]
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

    // suppression d'une photo
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
        $figure->removeFigureImage($figureImage);
        $entityManager->persist($figure);
        $entityManager->flush();

        return $this->redirectToRoute('figure_edit', array('id' => $figure->getId()));
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
// suppression d'une vidéo

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
        $figure->removeFigureVideo($figureVideo);
        $entityManager->persist($figure);
        $entityManager->flush();


        return $this->redirectToRoute('figure_edit', array('id' => $figure->getId()));
    }

    // suppression d'un commentaire par son auteur
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
        $this->addFlash("success", "La suppression a bien été effectuée");


        return $this->redirectToRoute('figure_show', ['slug' => $figure->getSlug()]);
    }
}
