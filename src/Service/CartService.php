<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;

class CartService
{
    private $requestStack;
    private $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    public function getCartItemsCount(): int
    {
        $session = $this->getSession();
        $cart = $session->get('cart', []);
        return array_sum($cart);
    }

    public function getCartItems(): array
    {
        $session = $this->getSession();
        $cart = $session->get('cart', []);
        $cartData = [];
        
        foreach ($cart as $productId => $quantity) {
            $product = $this->em->getRepository(Product::class)->find($productId);
            if ($product) {
                $cartData[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->getPrice() * $quantity,
                ];
            }
        }
        
        return $cartData;
    }

    public function getCartTotal(): float
    {
        $session = $this->getSession();
        $cart = $session->get('cart', []);
        $total = 0;
        
        foreach ($cart as $productId => $quantity) {
            $product = $this->em->getRepository(Product::class)->find($productId);
            if ($product) {
                $total += $product->getPrice() * $quantity;
            }
        }
        
        return $total;
    }
}
