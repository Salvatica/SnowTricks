<?php

namespace App\Entity;

use App\Repository\FigureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FigureRepository::class)
 */
class Figure
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
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;


    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="figure")
     */
    private $comment;


    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="figure")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $update_date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $figure_image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $figure_video;

    public function __construct()
    {

        $this->comment = new ArrayCollection();
        $this->created_at = new \DateTime();
        $this->update_date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }



    /**
     * @return Collection|Comment[]
     */
    public function getComment(): Collection
    {
        return $this->comment;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comment->contains($comment)) {
            $this->comment[] = $comment;
            $comment->setFigure($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comment->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getFigure() === $this) {
                $comment->setFigure(null);
            }
        }

        return $this;
    }


    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->update_date;
    }

    public function setUpdateDate(\DateTimeInterface $update_date): self
    {
        $this->update_date = $update_date;

        return $this;
    }

    public function getFigureImage(): ?string
    {
        return $this->figure_image;
    }

    public function setFigureImage(?string $figure_image): self
    {
        $this->figure_image = $figure_image;

        return $this;
    }

    public function getFigureVideo(): ?string
    {
        return $this->figure_video;
    }

    public function setFigureVideo(?string $figure_video): self
    {
        $this->figure_video = $figure_video;

        return $this;
    }
}
