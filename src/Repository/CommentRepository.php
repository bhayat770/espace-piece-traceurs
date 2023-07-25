<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function save(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findForPagination(?Product $product = null): Query
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC');

        if ($product) {
            $qb->leftJoin(' c.product', 'p') // Remplacez 'category' par le nom de la relation entre Product et Category
            ->where($qb->expr()->eq('p.id', ':productId'))
                ->setParameter('productId', $product->getId());
        }

        return $qb->getQuery();
    }

}
