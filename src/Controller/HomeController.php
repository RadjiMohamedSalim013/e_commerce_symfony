<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CategoryRepository $categoryRepository, ProductRepository $productRepository, CartService $cartService): Response
    {
        // Récupère toutes les catégories avec leurs produits
        $categories = $categoryRepository->findAll();
        
        // Organiser les produits par catégorie
        $productsByCategory = [];
        foreach ($categories as $category) {
            $products = $category->getProducts();
            if (count($products) > 0) {
                $productsByCategory[] = [
                    'category' => $category,
                    'products' => $products
                ];
            }
        }

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'productsByCategory' => $productsByCategory,
            'cartItemsCount' => $cartService->getCartItemsCount(),
        ]);
    }

    #[Route('/category/{id}', name: 'category_products')]
    public function category(Category $category, CartService $cartService): Response
    {
        // Récupère tous les produits de la catégorie
        $products = $category->getProducts();

        return $this->render('home/category.html.twig', [
            'category' => $category,
            'products' => $products,
            'cartItemsCount' => $cartService->getCartItemsCount(),
        ]);
    }

    #[Route('/product/{id}', name: 'product_show')]
    public function show(Product $product, CartService $cartService): Response
    {
        return $this->render('home/product_show.html.twig', [
            'product' => $product,
            'cartItemsCount' => $cartService->getCartItemsCount(),
        ]);
    }
}
