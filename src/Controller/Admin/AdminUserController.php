<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Service\UserManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'admin_user_list')]
    public function list(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $doctrine->getRepository(User::class)->findAll();

        return $this->render('admin/users/list.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/new', name: 'admin_user_new')]
    public function new(Request $request, UserManager $userManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->saveUser($user, $form->get('plainPassword')->getData());

            $this->addFlash('success', 'Utilisateur créé avec succès');
            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/users/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_user_edit')]
    public function edit(User $user, Request $request, UserManager $userManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->saveUser($user, $form->get('plainPassword')->getData());

            $this->addFlash('success', 'Utilisateur mis à jour avec succès');
            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/users/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_user_delete')]
    public function delete(User $user, UserManager $userManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $userManager->deleteUser($user);

        $this->addFlash('success', 'Utilisateur supprimé');

        return $this->redirectToRoute('admin_user_list');
    }
}
