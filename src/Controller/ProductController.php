<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;



#[Route('/product')]
class ProductController extends AbstractController
{


    private $TokenInterface;

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
        $fileDirectoryPath = $this->parameterBag->get('files_directory');
        $product = new Product();
        $user = $this->getUser();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $productFile = $form->get('fileZip')->getData();
            /**@var UploadedFile $productFile */
            if ($productFile) {
                //récupère le nom du fichier sans l'extension
                $originalFilename = pathinfo($productFile->getClientOriginalName(), PATHINFO_FILENAME);

                //slug le originalName, exemple :chat noir -> chat-noir
                $safeFileName = $slugger->slug($originalFilename);

                //vrai nom de fichier unique , exemple chat-noir -> chat-noir-jfhjsi752.jpg
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $productFile->guessExtension();
            }
            //on récupère les images
            $images = $form->get('images')->getData();
            //on boucle sur les images
            foreach ($images as $image) {
                //on génère un nom unique de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                //on copie le fichier dans le dossier upload images
                $image->move(
                    $this->parameterBag->get('images_directory'),
                    $fichier
                );
                //on stocke le nom de l'image dans la base de données
                $img = new Images();
                $img->setName($fichier);
                $product->addImage($img);
            }
            try {

                $productFile->move(
                    $fileDirectoryPath,
                    $newFileName
                );
                $product->setUser($user);
                $product->setFileZip($newFileName);
                $this->entityManager->persist($product);
                $this->entityManager->flush();
                dump($product);
            } catch (\Exception $e) {

            }
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'ProductController',
            'user'=> $user,
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
            //on récupère les images
            $images = $form->get('images')->getData();
            //on boucle sur les images
//            foreach($images as $image) {
//                //on génère un nom unique de fichier
//                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
//                //on copie le fichier dans le dossier upload images
//                $image->move(
//                    $this->getParameter('images_directory'),
//                    $fichier
//                );
//                //on stocke le nom de l'image dans la base de données
//                $img = new Images();
//                $img->setName($fichier);
//                $this->entityManager->persist($img);
//                $this->entityManager->flush();
//                $product->addImage($img);
//            }
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

    #[Route('/image/{id}/delete', name: 'app_image_delete', methods: ['DELETE'])]
    public function deleteImage(Images $image, Request $request, EntityManagerInterface $entityManager)
    {
        $data = json_decode($request->getContent(), true);
        //on vérifie si le token est valide
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data["_token"])) {
            //on récupère le nom de l'image
            $name = $image->getName();
            //on supprime le fichier
            unlink($this->getParameter('images_directory') . '/' . $name);

            //on supprime de la base

            $entityManager->persist($image);
            $entityManager->remove($image);
            $entityManager->flush();

            // on répond en json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
