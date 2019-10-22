<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @Route("/user/update", name="user_modify")
     */
    public function updateUser(Request $request, EntityManagerInterface $em,UserPasswordEncoderInterface $encoder){
        $user = $this->getUser();
        $password = $user->getPassword();

        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if (empty($user->getPassword())) {
            $user->setPassword($password);
        }


        if ($userForm->isSubmitted() && $userForm->isValid()) {
            try {
                $encoded = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($encoded);
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', "Votre profil a été modifié");
                return $this->redirectToRoute("user_profil");
            }
            catch (\Exception $ex) {
                $this->addFlash('danger', $ex->getMessage());
            }
        }
        return $this->render('user/update.html.twig', [
            "userForm" => $userForm->createView(),
        ]);
    }
}
