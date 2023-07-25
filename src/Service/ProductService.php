<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Tag;
use App\Repository\ProductRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductService
{
    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepository,
        private PaginatorInterface $paginator
    )
    {

    }
    public function getPaginatedArticles(?Category $category = null) : PaginationInterface
    {
        $request = $this->requestStack->getMainRequest();

        $page = $request->query->getInt('page', 1);
        $limit = 20;

        $productsQuery = $this->productRepository->findForPagination($category);

        return $this->paginator->paginate($productsQuery, $page, $limit);
    }

    public function findByProductsByTagAndCategory(Tag $tag, Category $category, $page = 1, $limit = 10)
    {
        $productsQuery = $this->productRepository->findByProductsByTagAndCategory($tag, $category);

        return $this->paginator->paginate($productsQuery, $page, $limit);
    }
    public function findByProductsByCategory(Category $category, $page = 1, $limit = 5)
    {
        $productsQuery = $this->productRepository->findByProductsByCategory($category);

        return $this->paginator->paginate($productsQuery, $page, $limit);
    }
}