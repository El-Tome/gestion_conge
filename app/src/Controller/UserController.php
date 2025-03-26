<?php
// src/Controller/UserController.php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    #[Route('/user', name: 'user')]
    public function List(): Response
    {
       // Utiliser la méthode findAll() pour récupérer tous les utilisateurs
        $users = $this->userRepository->findAll();
       
// Passer les utilisateurs à la vue
        return $this->render('user/index.html.twig', [
          //  'controller_name' => 'UserController',
           'users' => $users,
        ]);
    }

    #[Route('/user/{id}/delete', name: 'user_delete')]
    public function delete(User $user): Response
    {
        $this->userRepository->remove($user);
        return $this->redirectToRoute('user');
    }
}

