<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/account')]
class AccountController extends AbstractController
{
    #[Route('/', name: 'account')]
    public function index(): Response
    {
        // Ensure user is logged in
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $user = $this->getUser();
        
        return $this->render('account/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile', name: 'account_profile')]
    public function profile(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        return $this->render('account/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/orders', name: 'account_orders')]
    public function orders(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        return $this->render('account/orders.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/addresses', name: 'account_addresses')]
    public function addresses(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        return $this->render('account/addresses.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
