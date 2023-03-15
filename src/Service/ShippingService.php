<?php

// src/Service/ShippingService.php

namespace App\Service;

use App\Entity\ShippingPrice;
use Doctrine\ORM\EntityManagerInterface;

class ShippingService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getShippingPrice($country, $totalWeight)
    {
        // Récupère les frais d'expédition applicables pour le pays de destination et le poids total
        $shippingPrice = $this->entityManager->getRepository(ShippingPrice::class)
            ->createQueryBuilder('s')
            ->andWhere('s.pays = :pays')
            ->andWhere('s.poids >= :poids')
            ->orderBy('s.poids', 'ASC')
            ->setParameter('pays', $country)
            ->setParameter('poids', $totalWeight)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($shippingPrice) {
            return $shippingPrice->getPrice();
        } else {
            // Si aucun frais d'expédition n'a été trouvé pour le poids et le pays de destination, renvoie null ou une valeur par défaut.
            return null;
        }
    }
}
