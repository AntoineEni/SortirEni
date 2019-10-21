<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/userindex", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user", name="user_profil")
     */
    public function profilUser(){
        return $this->render('user/user.html.twig');
    }
    /**
     * @Route("/userUpdate", name="user_modify")
     */
    public function updateUser(){

    }
}
