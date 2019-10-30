<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user/index", name="user")
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
        $user = $this->getUser();
        return $this->render('user/user.html.twig',[
            'user'=>$user,
        ]);
    }
    /**
     * @Route("/user/update", name="user_modify")
     */
    public function updateUser(Request $request, EntityManagerInterface $em,UserPasswordEncoderInterface $encoder){
        $user = $this->getUser();
        $password = $user->getPassword();

        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        $userPasswordForm = $this->createForm(UserPasswordType::class, $user);
        $userPasswordForm->handleRequest($request);
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
        if ($userPasswordForm->isSubmitted() && $userPasswordForm->isValid()) {
            try {
                $encoded = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($encoded);
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', "Votre Mot de passe a été modifié");
                return $this->redirectToRoute("user_profil");
            }
            catch (\Exception $ex) {
                $this->addFlash('danger', $ex->getMessage());
            }
        }
        return $this->render('user/update.html.twig', [
            "userForm" => $userForm->createView(),
            "userPasswordForm" => $userPasswordForm->createView(),
        ]);
    }

    /**
     * @Route("/user/detail/{pseudo}", name="user_by_pseudo")
     */
    public function userByPseudo($pseudo){
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username'=>$pseudo]);
        return $this->render('user/user.html.twig',[
            'user'=>$user,
        ]);
    }
}
