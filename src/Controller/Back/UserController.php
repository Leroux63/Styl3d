<?php

namespace App\Controller\Back;

use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    #[Route('/user/{id}', name: 'app_user')]
    public function index(int $id,UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        return $this->render('back/user/show.html.twig', [
            'user' => $user,

        ]);
    }
}