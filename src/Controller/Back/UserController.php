<?php

namespace App\Controller\Back;

use App\Entity\Article;
use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\CartType;
use App\Form\UserType;
use App\Repository\ArticleRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{

    public function __construct(

    )
    {
    }


//    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
//    {
//        $form = $this->createForm(ArticleType::class, $article);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $articleRepository->save($article, true);
//
//            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->renderForm('article/edit.html.twig', [
//            'article' => $article,
//            'form' => $form,
//        ]);
//    }
    #[Route('/user/{id}/edit', name: 'profile_user_edit', methods: ['GET', 'POST'])]
    public function updateUser(int $id,User $user, Request $request,EntityManagerInterface $entityManager): Response
    {
//        if (!$this->getUser()){
//            return $this->redirectToRoute('app_login');
//        }
//        if (!$this->getUser() != $user){
//            return $this->redirectToRoute('app_home');
//        }
        $user=$this->getUser();
        $form = $this->createForm(UserType::class,$user);
        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Les informations du compte ont été mises à jour'
            );
            return $this->redirectToRoute('app_home');

        }
        return $this->render('back/user/edit.html.twig', [
            'form' => $form->createView(),

        ]);

    }
    #[Route('/user/{id}', name: 'profile_user', methods: ['GET', 'POST'])]
    public function updateCart(int $id, CartRepository $cartRepository,UserRepository $userRepository, Request $request): Response
    {
        $user = $userRepository->find($id);
        $cart = $cartRepository->find($id);
        dump($cart);
        $formCart = $this->createForm(CartType::class, $cart);
        $formCart->handleRequest($request);
        if ($formCart->isSubmitted() && $formCart->isValid()) {

            $cartRepository->save($cart,true);
            dump($cart);
            return $this->redirectToRoute('profile_user',['id' => $user->getId()]);
        }
        return $this->render('back/user/show.html.twig', [
            'user' => $user,
            'cart' => $cart,
            'formCart' => $formCart->createView(),
        ]);

    }
    #[Route('/product/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {

        $products = $productRepository->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products,

        ]);
    }

//    #[Route('/user/{id}', name: 'app_user')]
//    public function index(int $id, UserRepository $userRepository): Response
//    {
//        $user = $userRepository->find($id);
//
//        return $this->render('back/user/show.html.twig', [
//            'user' => $user,
//
//        ]);
//    }


//    #[Route('/user/product/{id}', name: 'app_product_delete', methods: ['POST'])]
//    #[IsGranted('ROLE_USER')]
//    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
//    {
//        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
//            $images = $product->getImages();
//            $productFile = $product->getFileZip();
//
//            foreach ($images as $image) {
//                dump($image);
//                //on récupère le nom de l'image
//                $name = $image->getName();
//                //on supprime le fichier
//                unlink($this->getParameter('images_directory') . '/' . $name);
//
//            }
//            unlink($this->getParameter('files_directory') . '/' . $productFile);
//            $productRepository->remove($product, true);
//
//        }
//
//        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
//    }



}