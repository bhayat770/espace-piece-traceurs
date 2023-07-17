<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

class CommentController extends AbstractController
{

    private $tokenStorage;
    private $security;

    public function __construct(TokenStorageInterface $tokenStorage, Security $security)
    {
        $this->tokenStorage = $tokenStorage;
        $this->security = $security;

    }



    #[Route('/ajax/comments', name: 'app_comment_add')]
    public function add(TokenStorageInterface $tokenStorage, Request $request,CommentRepository $commentRepository, Cart $cart, ProductRepository $productRepository,EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {

        $commentData = $request->request->all('comment');

        if (!$this->isCsrfTokenValid('comment-add', $commentData['_token'])) {
            return $this->json([
                'code'=>'INVALID_CSRF_TOKEN',
            ],Response::HTTP_BAD_REQUEST);
        }
        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        $produit = $productRepository->findOneBy(['id' => $commentData['product']]);
        if (!$produit) {
            return $this->json([
                'code'=>'ARTICLE_NOT_FOUND'
            ], Response::HTTP_BAD_REQUEST);
        }
        $comment = new Comment($produit);
        $comment->setContent($commentData['content']);

        // Récupérer l'utilisateur connecté
        $user = $this->security->getUser();

        // Si l'utilisateur n'est pas connecté, obtenir un utilisateur aléatoire
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $userRepository->findRandomUser();
        } else{

        $comment->setUser($user);
        }
        $comment->setCreatedAt(new \DateTime());

        $entityManager->persist($comment);
        $entityManager->flush();


        $html = $this->renderView('comment/index.html.twig', [
            'comment' => $comment,
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart' => $cart->getFull(),
        ]);

        return $this->json([
            'code'=>'COMMENT_ADDED_SUCCESSFULlY',
            'message'=>$html,
            'numberOfComments'=> $commentRepository->count(['product'=>$produit])
        ]);
    }
}
