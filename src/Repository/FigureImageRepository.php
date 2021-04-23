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

    // /**
    //  * @return FigureImage[] Returns an array of FigureImage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FigureImage
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
