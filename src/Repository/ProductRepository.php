<?php

namespace App\Repository;

use App\Classe\Search;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Traceurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Tag;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function __construct(ManagerRegistry $registry, SluggerInterface $slugger)
    {
        parent::__construct($registry, Product::class);
        $this->slugger = $slugger;

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

    public function findByCategory(Category $category): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->where('c.id = :categoryId')
            ->setParameter('categoryId', $category->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     # Requête pour reccupérer produits en fonction de la recherche de l'user
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

        if (!empty($search->tags)) {
            $query = $query
                ->join('p.tags', 't')
                ->andWhere('t.id IN (:tags)')
                ->setParameter('tags', $search->tags);
        }
        if (!empty($search->minPrice)) {
            $query = $query
                ->andWhere('p.price >= :minPrice')
                ->setParameter('minPrice', $search->minPrice);
        }

        if (!empty($search->maxPrice)) {
            $query = $query
                ->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', $search->maxPrice);
        }


        return $query->getQuery()->getResult();
    }


    public function findSimilarProductsByTags($tags, $currentProduct, $limit = 4)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->andWhere($qb->expr()->neq('p', ':currentProduct'))
            ->setParameter('currentProduct', $currentProduct);

        $qb->innerJoin('p.tags', 't', Join::WITH, $qb->expr()->in('t', ':tags'))
            ->setParameter('tags', $tags);

        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }


    /**
     * Récupère tous les produits ayant comme tag le traceur donné.
     *
     * @param Traceurs $traceur
     * @return Product[]
     */
    public function findByTag(string $tag): array
    {
        $slug = $this->slugger->slug($tag)->lower();
        return $this->createQueryBuilder('p')
            ->join('p.tags', 't')
            ->where('t.nom = :tag') // Modifier 't.slug' en 't.nom'
            ->setParameter('tag', $tag)
            ->getQuery()
            ->getResult();

    }


    public function findForPagination(?Category $category = null): Query
    {
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC');

        if ($category){
            $qb->leftJoin('a.categories', 'c')
                ->where($qb->expr()->eq('c.id', ':categoryId'))
                ->setParameter('categoryId', $category->getId())
            ;
        }
        return $qb->getQuery();

    }

    public function getProductsByTag(Tag $tag)
    {
        // Récupérer les produits associés à la catégorie et au tag
        $products = [];
        foreach ($this->products as $product) {
            if ($product->hasTag($tag)) {
                $products[] = $product;
            }
        }
    }
    public function findByProductsByTagAndCategory(Tag $tag, Category $category)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->innerJoin('p.tags', 't')
            ->innerJoin('p.category', 'c')
            ->where($qb->expr()->eq('t.id', ':tagId'))
            ->andWhere($qb->expr()->eq('c.id', ':categoryId'))
            ->setParameter('tagId', $tag->getId())
            ->setParameter('categoryId', $category->getId());

        return $qb->getQuery()->getResult();
    }
    public function findByProductsByCategory(Category $category): Query
    {
        $qb = $this->createQueryBuilder('p');
        $qb->innerJoin('p.category', 'c')
            ->where($qb->expr()->eq('c.id', ':categoryId'))
            ->setParameter('categoryId', $category->getId());

        return $qb->getQuery();
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
