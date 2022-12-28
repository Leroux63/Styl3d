<?php

namespace App\Controller\Back;

use App\Entity\Cart;
use App\Form\CartType;
use App\Repository\CartRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/user/{id}', name: 'update_cart_user', methods: ['GET', 'POST'])]
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
            return $this->redirectToRoute('app_user',['id' => $user->getId()]);
        }
        return $this->render('back/user/show.html.twig', [
            'user' => $user,
            'cart' => $cart,
            'formCart' => $formCart->createView(),
        ]);

    }
    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleRepository->save($article, true);

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/user/{id}', name: 'app_user')]
    public function index(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        return $this->render('back/user/show.html.twig', [
            'user' => $user,

        ]);
    }





}