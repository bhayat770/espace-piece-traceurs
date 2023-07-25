<?php

namespace App\Repository;

use App\Entity\Traceurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Traceurs>
 *
 * @method Traceurs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Traceurs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Traceurs[]    findAll()
 * @method Traceurs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TraceursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Traceurs::class);
    }

    public function save(Traceurs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Traceurs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function getDistinctMarques()
    {
        $qb = $this->createQueryBuilder('t')
            ->select('DISTINCT t.marque')
            ->orderBy('t.marque', 'ASC');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getDistinctSeriesByMarque($marque)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('DISTINCT t.serie')
            ->where('t.marque = :marque')
            ->setParameter('marque', $marque)
            ->orderBy('t.serie', 'ASC');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getDistinctModelesByMarqueAndSerie($marque, $serie)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('DISTINCT t.modele')
            ->where('t.marque = :marque')
            ->andWhere('t.serie = :serie')
            ->setParameter('marque', $marque)
            ->setParameter('serie', $serie)
            ->orderBy('t.reference', 'ASC');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getCodesPanesByMarqueSerieAndModele($marque, $serie, $reference)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t.codePane')
            ->where('t.marque = :marque')
            ->andWhere('t.serie = :serie')
            ->andWhere('t.reference = :reference')
            ->setParameter('marque', $marque)
            ->setParameter('serie', $serie)
            ->setParameter('reference', $reference);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getTraceurByModele()
    {
        return $this->createQueryBuilder('t')
            ->where('t.nom LIKE :nom')
            ->setParameter('nom', '%DesignJet%')
            ->getQuery()
            ->getResult();
    }



//    /**
//     * @return Traceurs[] Returns an array of Traceurs objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Traceurs
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
