<?php
namespace App\Manager;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\FigureImage;
use App\Entity\FigureVideo;
use App\Entity\User;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\VideoLinkSanitizer;

class FigureManager
{
    /**
     * FigureManager constructor.
     */
    public function __construct(
        private FileUploader $fileUploader,
        private EntityManagerInterface $entityManager,
        private VideoLinkSanitizer $videoLinkSanitizer)
    {
    }

    /**
     * @param $photos
     * @param Figure $figure
     */
    public function handleImages($photos,Figure $figure)
    {
        foreach ($photos as $photo) {
            $fileName = $this->fileUploader->upload($photo);
            $figureImage = new FigureImage;
            $figureImage->setFileName($fileName);
            $figure->addFigureImage($figureImage);
        }
    }

    public function handleVideos($videos, Figure $figure)
    {
        foreach ($videos as $video) {
            $url = $video->getFileName();
            $video->setFileName($this->videoLinkSanitizer->clean($url));
            $video->setFigure($figure);
        }
    }

    public function savePhoto(Figure $figure, FigureImage $figureImage, $photo){
        $fileName = $this->fileUploader->upload($photo);
        $figureImage->setFileName($fileName);
        $figureImage->setFigure($figure);

        $this->entityManager->flush();
    }

    public function saveFigure(Figure $figure){
        $this->entityManager->persist($figure);
        $this->entityManager->flush();
    }

    public function addComment(Figure $figure,User $user, Comment $comment)
    {
        $comment->setFigure($figure);
        $comment->setUser($user);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

}