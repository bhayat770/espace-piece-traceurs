<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]

    public function index(Request $request, PersistenceManagerRegistry $doctrine, UserPasswordHasherInterface $hasher): Response
    {

        $notification = null;


        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $password = $hasher->hashPassword($user, $user->getPassword()) ;

            $user->setPassword($password);

            $em = $doctrine->getManager();
            $em->persist($user); //fige la donnée
            $em->flush();
          //  $notification = "Votre mot de passe a bien été mis à jour";
        }
       // else
       // {
        //    $notification = "Votre mot de passe actuel n'est pas le bon";
        //}


        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            //'notification' => $notification,

        ]);

    }
}
