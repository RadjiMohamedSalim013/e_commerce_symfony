<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'cart_index')]
    public function index(CartService $cartService): Response
    {
        $cartData = $cartService->getCartItems();
        $total = $cartService->getCartTotal();

        return $this->render('cart/index.html.twig', [
            'cart' => $cartData,
            'total' => $total,
            'cartItemsCount' => $cartService->getCartItemsCount(),
        ]);
    }

    #[Route('/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function add(Product $product, Request $request, SessionInterface $session, CartService $cartService): Response
    {
        $quantity = (int)$request->request->get('quantity', 1);

        $cart = $session->get('cart', []);
        $cart[$product->getId()] = ($cart[$product->getId()] ?? 0) + $quantity;

        $session->set('cart', $cart);

        $this->addFlash('success', 'Produit ajouté au panier !');

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/remove/{id}', name: 'cart_remove')]
    public function remove(Product $product, SessionInterface $session, CartService $cartService): Response
    {
        $cart = $session->get('cart', []);
        if (isset($cart[$product->getId()])) {
            unset($cart[$product->getId()]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/decrement/{id}', name: 'cart_decrement')]
    public function decrement(Product $product, SessionInterface $session, CartService $cartService): Response
    {
        $cart = $session->get('cart', []);
        if (isset($cart[$product->getId()]) && $cart[$product->getId()] > 1) {
            $cart[$product->getId()]--;
        } elseif (isset($cart[$product->getId()])) {
            unset($cart[$product->getId()]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/increment/{id}', name: 'cart_increment')]
    public function increment(Product $product, SessionInterface $session, CartService $cartService): Response
    {
        $cart = $session->get('cart', []);
        $cart[$product->getId()] = ($cart[$product->getId()] ?? 0) + 1;

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart_index');
    }
}
