<?php

namespace App\Repository;

use App\Classe\Search;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * Requête pour reccupérer produits en fonction de la recherche de l'user
     * @return Product[]
     */
    public function findWithSearch (Search $search)
    {
        $query = $this //
            ->createQueryBuilder('p') //methode pour créer une requete
            ->select('c','p') //selectionner categories et products
            ->join('p.category', 'c'); //on joint les produits et catégories

        if (!empty($search->categories)) { //Si c'est pas vide alors executer
            $query = $query
                ->andWhere('c.id IN (:categories)') //on passe un parametre au where
                ->setParameter('categories', $search->categories); //on définit le parametre en 2eme param on lui donne la valeur de cette clé
        }

        if (!empty($search->string)) {
            $query =$query
                ->andWhere('p.name LIKE :string') //le parametre permet de prévenir les injections sql
                ->setParameter('string',  "%{$search->string}%"); //Les caractères "%" avant et après la chaîne de recherche permettent de chercher des occurrences de la chaîne n'importe où dans le nom du produit, et non pas seulement au début ou à la fin
        }

        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
