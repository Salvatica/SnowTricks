<?php

namespace App\Entity;

use App\Repository\FigureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Valid;


/**
 * @ORM\Entity(repositoryClass=FigureRepository::class)
 * @UniqueEntity("name", message="Cette figure existe déjà")
 * @ORM\HasLifecycleCallbacks()
 *
 */
class Figure
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private mixed $slug;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="figure", cascade={"persist","remove"})
     */
    private $comments;

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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $update_date;

    /**
     * @ORM\OneToMany(targetEntity=FigureImage::class, mappedBy="figure", orphanRemoval=true, cascade={"persist"})
     */
    private $figureImages;

    /**
     * @ORM\OneToMany(targetEntity=FigureVideo::class, mappedBy="figure", orphanRemoval=true, cascade={"persist"})
     */
    #[Valid()]
    private $figureVideos;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->created_at = new \DateTime();
        $this->update_date = new \DateTime();
        $this->figureImages = new ArrayCollection();
        $this->figureVideos = new ArrayCollection();
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

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
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
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setFigure($this);
        }
        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
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

    /**
     * @return Collection|FigureImage[]
     */
    public function getFigureImages(): Collection
    {
        return $this->figureImages;
    }

    public function addFigureImage(FigureImage $figureImage): self
    {
        if (!$this->figureImages->contains($figureImage)) {
            $this->figureImages[] = $figureImage;
            $figureImage->setFigure($this);
        }
        return $this;
    }

    public function removeFigureImage(FigureImage $figureImage): self
    {
        if ($this->figureImages->removeElement($figureImage)) {
            // set the owning side to null (unless already changed)
            if ($figureImage->getFigure() === $this) {
                $figureImage->setFigure(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|FigureVideo[]
     */
    public function getFigureVideos(): Collection
    {
        return $this->figureVideos;
    }

    public function addFigureVideo(FigureVideo $figureVideo): self
    {
        if (!$this->figureVideos->contains($figureVideo)) {
            $this->figureVideos[] = $figureVideo;
            $figureVideo->setFigure($this);
        }
        return $this;
    }

    public function removeFigureVideo(FigureVideo $figureVideo): self
    {
        if ($this->figureVideos->removeElement($figureVideo)) {
            // set the owning side to null (unless already changed)
            if ($figureVideo->getFigure() === $this) {
                $figureVideo->setFigure(null);
            }
        }
        return $this;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function setUpdateDateValue()
    {
        $this->setUpdateDate(new \DateTime());
    }

    /**
     * @ORM\PrePersist()
     */
    public function setCreatedAtValue()
    {
        $this->setCreatedAt(new \DateTime());
    }
}
