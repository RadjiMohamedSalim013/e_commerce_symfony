<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_list')]
    public function list(ManagerRegistry $doctrine, CartService $cartService): Response
    {
        $products = $doctrine->getRepository(Product::class)->findAll();

        return $this->render('product/list.html.twig', [
            'products' => $products,
            'cartItemsCount' => $cartService->getCartItemsCount(),
        ]);
    }

    #[Route('/products/{id}', name: 'product_detail')]
    public function detail(Product $product, CartService $cartService): Response
    {
        return $this->render('product/detail.html.twig', [
            'product' => $product,
            'cartItemsCount' => $cartService->getCartItemsCount(),
        ]);
    }

    
}
