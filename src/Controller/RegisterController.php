<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/inscription', name: 'app_register')]

    public function index(Request $request, PersistenceManagerRegistry $doctrine, UserPasswordHasherInterface $hasher, Cart $cart): Response
    {

        $notification = null;


        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $search_mail = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if (!$search_mail)
            {

                $password = $hasher->hashPassword($user, $user->getPassword()) ;

                $user->setPassword($password);

                $em = $doctrine->getManager();
                $em->persist($user); //fige la donnée
                $em->flush();

                $mail = new Mail();
                $content = "Bonjour,".$user->getFirstname()."<br/>Bienvenue sur votre boutique de pièces détachées de traceurs<br><br> Vous pouvez acheter des pièces détachées HP, Canon, Epson et bien d'autres";
                $mail->send($user->getEmail(), $user->getFirstname(), 'Bienvenue sur la boutique Inforiel', $content);

                $notification = "Votre inscription s'est correctement déroulée. Vous pouvez dès à présent vous connecter à votre compte";

            }
            else
            {

                $notification = "L'email que vous avez renseigné existe déjà.";

            }

          //  $notification = "Votre mot de passe a bien été mis à jour";
        }
       // else
       // {
        //    $notification = "Votre mot de passe actuel n'est pas le bon";
        //}

        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart'=>$cart->getFull(),
            'notification' => $notification,

        ]);

    }
}
