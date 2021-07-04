<?php

namespace App\Repository;

use App\Entity\FigureImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FigureImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method FigureImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method FigureImage[]    findAll()
 * @method FigureImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FigureImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FigureImage::class);
    }

}
