<?php
// src/Command/AddShippingPricesCommand.php

namespace App\Command;

use App\Entity\ShippingPrice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddShippingPricesCommand extends Command
{
    // Le nom de la commande (utilisé pour appeler la commande depuis la ligne de commande)
    protected static $defaultName = 'app:add-shipping-prices';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Ajoute des frais d\'expédition pour différents pays et poids')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $shippingPrices = [
            // France
            ['pays' => 'France', 'poids' => 0.5, 'price' => 5.00],
            ['pays' => 'France', 'poids' => 1.0, 'price' => 7.00],
            ['pays' => 'France', 'poids' => 2.0, 'price' => 9.00],
            // Italie
            ['pays' => 'Italie', 'poids' => 0.5, 'price' => 6.00],
            ['pays' => 'Italie', 'poids' => 1.0, 'price' => 8.00],
            ['pays' => 'Italie', 'poids' => 2.0, 'price' => 12.00],
            // ...
        ];

        foreach ($shippingPrices as $shippingPriceData) {
            $shippingPrice = new ShippingPrice();
            $shippingPrice->setPays($shippingPriceData['pays']);
            $shippingPrice->setPoids($shippingPriceData['poids']);
            $shippingPrice->setPrice($shippingPriceData['price']);

            $this->entityManager->persist($shippingPrice);
        }

        $this->entityManager->flush();

        $output->writeln('Frais d\'expédition ajoutés avec succès !');

        return Command::SUCCESS;
    }
}
