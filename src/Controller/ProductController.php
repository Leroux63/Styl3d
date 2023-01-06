<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Comments;
use App\Entity\Images;
use App\Entity\Product;
use App\Entity\Rating;
use App\Form\CartType;
use App\Form\CommentsType;
use App\Form\EditProductType;
use App\Form\ProductType;
use App\Form\RatingType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Symfony\Component\String\Slugger\SluggerInterface;
use function PHPUnit\Framework\returnValue;


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

    #[Route('/{id}', name: 'app_product_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $getAvgByProduct = $productRepository->getAverageRatingByProduct();
        $user = $this->getUser();
        //partie panier Upload
        $cart = new Cart();
        $productFile = $product->getFileZip();
        $filePath = $this->getParameter('files_directory') . '/' . $productFile;

        //partie commentaire
        $comment = new Comments();
        $score = new Rating();
        //on génère les formulaires
        $commentForm = $this->createForm(CommentsType::class, $comment);
        $commentForm->handleRequest($request);

        $scoreForm = $this->createForm(RatingType::class, $score);
        $scoreForm->handleRequest($request);

        $cartForm = $this->createForm(CartType::class, $cart);
        $cartForm->handleRequest($request);

        //traitement formulaire
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setProduct($product);
            $comment->setUser($user);
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
            return $this->redirectToRoute('galeries', [], Response::HTTP_SEE_OTHER);
        }
        if ($scoreForm->isSubmitted() && $scoreForm->isValid()) {
            $score->setProduct($product);
            $score->setUser($user);
            $this->entityManager->persist($score);
            $this->entityManager->flush();
            return $this->redirectToRoute('galeries', [], Response::HTTP_SEE_OTHER);
        }
        if ($cartForm->isSubmitted() && $cartForm->isValid()) {
            $cart->setProduct($product);
            $cart->setUser($user);
            $cart->setDownloaded(1);
            $this->entityManager->persist($cart);
            $this->entityManager->flush();
            dump($cart);

            // Je crée une réponse de type BinaryFileResponse
            $response = new BinaryFileResponse($filePath);

            // Je configure la réponse en ajoutant un en-tête "Content-Disposition" avec le type "attachment" et le nom du fichier
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, basename($filePath));

            // J'envoie la réponse au navigateur pour déclencher le téléchargement
            return $response;
//          return $this->redirectToRoute('app_user', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'user' => $user,
            'commentForm' => $commentForm->createView(),
            'scoreForm' => $scoreForm->createView(),
            'cartForm' => $cartForm->createView(),
            'getAvgByProduct' => $getAvgByProduct,
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
        $productFile = $form->get('fileZip')->getData();
        if ($form->isSubmitted() && $form->isValid()) {


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
            'user' => $user,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_product_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $images = $product->getImages();
            $productFile = $product->getFileZip();

            foreach ($images as $image) {
                dump($image);
                //on récupère le nom de l'image
                $name = $image->getName();
                //on supprime le fichier
//                unlink($this->getParameter('images_directory') . '/' . $name);

            }
//            unlink($this->getParameter('files_directory') . '/' . $productFile);
            $productRepository->remove($product, true);

        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {

        $products = $productRepository->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products,

        ]);
    }





    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(int $id, Request $request, ProductRepository $productRepository): Response
    {
        //on vérifie si l'utilisateur peut éditer avec le voter

//        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);
        $product = $productRepository->find($id);
//        $productFile= $product->getFileZip();
//        dump($productFile);
//        $filePath=$this->getParameter('files_directory') . '/' . $productFile;
//        $product = $productRepository->find($id);
        $user = $this->getUser();
        $form = $this->createForm(EditProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

//            on récupère les images
            $images = $form->get('images')->getData();
//            on boucle sur les images
            foreach ($images as $image) {
                //on génère un nom unique de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                //on copie le fichier dans le dossier upload images
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                //on stocke le nom de l'image dans la base de données
                $img = new Images();
                $img->setName($fichier);
                $product->addImage($img);
                $this->entityManager->persist($img);


            }

//            $product->setFileZip($filePath);
            $this->entityManager->flush();
            dump($product);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
            'user' => $user,
        ]);
    }


    #[Route('/image/{id}/delete', name: 'app_image_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deleteImage(Images $image, Request $request, EntityManagerInterface $entityManager): JsonResponse
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
