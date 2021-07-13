<?php
namespace App\Manager;

use App\Entity\Figure;
use App\Entity\FigureImage;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;

class FigureManager
{


    /**
     * FigureManager constructor.
     */
    public function __construct(
        private FileUploader $fileUploader,
        private EntityManagerInterface $entityManager)
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

    public function savePhoto(Figure $figure, FigureImage $figureImage, $photo){
        $fileName = $this->fileUploader->upload($photo);
        $figureImage->setFileName($fileName);
        $figureImage->setFigure($figure);

        $this->entityManager->flush();
    }

}