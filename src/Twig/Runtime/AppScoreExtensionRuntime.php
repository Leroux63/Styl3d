<?php

namespace App\Twig\Runtime;

use App\Entity\Product;
use App\Entity\Rating;
use App\Repository\ProductRepository;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

class AppScoreExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private Environment $environment
    )
    {
        // Inject dependencies if needed
    }



    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
//    public function getStarsHtml(Product $product)
//    {
//
//        $html = $this->environment->render('partials/_starsScoreProduct.html.twig',[
//            'product' => $product
//        ]);
//
//        return $html;
//    }
}
