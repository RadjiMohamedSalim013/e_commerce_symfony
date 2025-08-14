<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;


final class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {


        if ($request->isMethod('POST')) {
            $user = new User();
            $user->setEmail($request->request->get('email'));
            $user->setFirstname($request->request->get('firstname'));
            $user->setLastname($request->request->get('lastname'));


            // Hashage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $request->request->get('password')
            );
            $user->setPassword($hashedPassword);

            // attribuer le role USER
            $user->setRoles(['ROLE_USER']);

            // Enregistrement de la date et l'heure
            $user->setCreatedAt(new \DateTimeImmutable());

            // Enregistrement de l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            //redirection vers la page de connexion
            return $this->redirectToRoute('login');
        }

        // Affichage du formulaire d'inscription
        return $this->render('registration/register.html.twig',);
    }



























    public function index(): Response
    {
        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
        ]);
    }
}
