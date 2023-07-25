<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\Wishlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wishlist>
 *
 * @method Wishlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wishlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wishlist[]    findAll()
 * @method Wishlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WishlistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wishlist::class);
    }

    public function save(Wishlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Wishlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getWishlistByUser(User $user): ?Wishlist
    {
        return $this->createQueryBuilder('w')
            ->where('w.user = :user')
            ->leftJoin('w.product', 'p') // Si la relation est nommée "products"
            ->addSelect('p') // Si la relation est nommée "products"
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function removeProductWishlist(int $wishlistId, User $user, EntityManagerInterface $entityManager): ?Wishlist
    {
        return $entityManager->getRepository(Wishlist::class)
            ->createQueryBuilder('w')
            ->where('w.id = :wishlistId AND w.user = :user')
            ->setParameter('wishlistId', $wishlistId)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }




//    /**
//     * @return Wishlist[] Returns an array of Wishlist objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Wishlist
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
