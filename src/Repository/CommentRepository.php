<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Figure;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 10; // nombre de commentaires par pages

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function getCommentPaginator(Figure $figure, int $offset): Paginator //pagination comments
    {
            $query = $this->createQueryBuilder('c')
                ->andWhere('c.figure = :figure')
                ->setParameter('figure', $figure)
                ->orderBy('c.date', 'DESC')
                ->setMaxResults(self::PAGINATOR_PER_PAGE)
                ->setFirstResult($offset)
                ->getQuery();


            return new Paginator($query);

    }
}
