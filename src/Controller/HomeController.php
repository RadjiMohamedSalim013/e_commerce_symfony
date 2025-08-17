<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CategoryRepository $categoryRepository, ProductRepository $productRepository): Response
    {
        // Récupère toutes les catégories
        $categories = $categoryRepository->findAll();

        // Récupère les 8 produits les plus récents
        $products = $productRepository->findBy([], ['id' => 'DESC'], 8);

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    #[Route('/category/{id}', name: 'category_products')]
    public function category(Category $category): Response
    {
        // Récupère tous les produits de la catégorie
        $products = $category->getProducts();

        return $this->render('home/category.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);
    }

    #[Route('/product/{id}', name: 'product_show')]
    public function show(Product $product): Response
    {
        return $this->render('home/product_show.html.twig', [
            'product' => $product,
        ]);
    }
}
