<?php

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BreadcrumbService
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function generateBreadcrumb(array $pageInfo): array
    {
        $breadcrumb[] = [
            'label' => 'Accueil',
            'route' => 'app_home',
            'params' => [],
        ];

        $breadcrumb[] = [
            'label' => 'Nos Produits',
            'route' => 'app_products',
            'params' => [],
        ];

        $breadcrumb[] = [
            'label' => $pageInfo['pageName'],
            'route' => 'app_product',
            'params' => ['slug' => $pageInfo['slug']],
        ];
        return $breadcrumb;

    }
}
