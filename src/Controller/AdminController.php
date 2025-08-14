<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Controller\User;
use App\Entity\User as EntityUser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\UserType;



final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response

    {

        // verification si l'utilisateur a le role admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');


        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/users', name: 'admin_users')]
    public function users(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupérer le repository User
        $userRepository = $doctrine->getRepository(EntityUser::class);

        $users = $userRepository->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('admin/users/new', name: 'admin_users_new')]
    public function newUser(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {

        // Vérification si l'utilisateur a le rôle admin
        $this->denyAccessUnlessGranted(('ROLE_ADMIN'));

        $user = new EntityUser();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();

            //attribuer le role ROLE_USER par défaut
            $data->setRoles(['ROLE_USER']);

            //enregistré la date de création
                $user->setCreatedAt(new \DateTimeImmutable());


            // Hasher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $data->getPassword());
            $user->setPassword($hashedPassword);

            // Enregistrer l'utilisateur dans la base de données
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/new_user.html.twig', [
            'form' => $form->createView(),
        ]);



    }
}