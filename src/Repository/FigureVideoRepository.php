<?php

namespace App\Repository;

use App\Entity\FigureVideo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FigureVideo|null find($id, $lockMode = null, $lockVersion = null)
 * @method FigureVideo|null findOneBy(array $criteria, array $orderBy = null)
 * @method FigureVideo[]    findAll()
 * @method FigureVideo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FigureVideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FigureVideo::class);
    }

}
