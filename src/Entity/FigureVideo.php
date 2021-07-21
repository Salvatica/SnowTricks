<?php

namespace App\Entity;

use App\Repository\FigureVideoRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\FigureVideoConstraint as FigureVideoConstraint;


/**
 * @ORM\Entity(repositoryClass=FigureVideoRepository::class)
 */
class FigureVideo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[FigureVideoConstraint()]
    private ?string $fileName;

    /**
     * @ORM\ManyToOne(targetEntity=Figure::class, inversedBy="figureVideos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $figure;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function getFigure(): ?Figure
    {
        return $this->figure;
    }

    public function setFigure(?Figure $figure): self
    {
        $this->figure = $figure;
        return $this;
    }
}
