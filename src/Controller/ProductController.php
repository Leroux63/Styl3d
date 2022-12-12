<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/product')]
class ProductController extends AbstractController
{
    public function __construct(
        //on se sert du parameter bag pour recuperer les infos du service yaml
        private ParameterBagInterface  $parameterBag,
        private EntityManagerInterface $entityManager,
        // private PaginatorInterface     $paginator
    )
    {
    }

    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $imageDirectoryPath = $this->parameterBag->get('images_directory');
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $productImage= $form->get('imageFile')->getData();
            /**@var UploadedFile $productImage */
            if ($productImage){
                //récupère le nom du fichier sans l'extension
                $originalFilename = pathinfo($productImage->getClientOriginalName(), PATHINFO_FILENAME);

                //slug le originalName, exemple :chat noir -> chat-noir
                $safeFileName = $slugger->slug($originalFilename);

                //vrai nom de fichier unique , exemple chat-noir -> chat-noir-jfhjsi752.jpg
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $productImage->guessExtension();
            }
            try {
                $productImage->move(
                    $imageDirectoryPath,
                    $newFileName
                );
                $product->setImg($newFileName);
                $this->entityManager->persist($product);
                $this->entityManager->flush();
                dump($product);
            } catch (\Exception $e) {

            }
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
