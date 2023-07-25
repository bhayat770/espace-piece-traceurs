<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CommentService
{
    public function __construct(
        private RequestStack $requestStack,
        private CommentRepository $commentRepository,
        private PaginatorInterface $paginator
    )
    {}
    public function getPaginatedComments(?Product $product = null) :PaginationInterface
    {
        $request = $this->requestStack->getMainRequest();

        $page = $request->query->getInt('page', 1);
        $limit = 2;

        $commentsQuery = $this->commentRepository->findForPagination($product);

        return $this->paginator->paginate($commentsQuery, $page, $limit);
    }

}