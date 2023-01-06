<?php

namespace App\Controller;

use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository, ProductCategoryRepository $productCategoryRepository): Response
    {
        $products = $productRepository->findAll();
        $lastProductsCreated = $productRepository->getLastProducts();
        $productCategories = $productCategoryRepository->findAll();
        return $this->render('home/index.html.twig', [
            'products' => $products,
            'product_categories' => $productCategories,
            'lastProducts' => $lastProductsCreated,
        ]);
    }
}
