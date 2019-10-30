<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Manage User
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Show the profile of current user
     * @Route("/user", name="user_profil", methods={"GET"})
     */
    public function profileUser() {
        $user = $this->getUser();

        return $this->render('user/user.html.twig',[
            'user' => $user,
        ]);
    }

    /**
     * Update current user
     * @Route("/user/update", name="user_modify", methods={"GET"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     */
    public function updateUser(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder) {
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

                $this->addFlash('success', $this->translator->trans("user.update.success"));
                return $this->redirectToRoute("user_profil");
            }
            catch (Exception $ex) {
                $this->addFlash('danger', $ex->getMessage());
            }
        }

        if ($userPasswordForm->isSubmitted() && $userPasswordForm->isValid()) {
            try {
                $encoded = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($encoded);

                $em->persist($user);
                $em->flush();

                $this->addFlash('success', $this->translator->trans("user.update.success"));
                return $this->redirectToRoute("user_profil");
            }
            catch (Exception $ex) {
                $this->addFlash('danger', $ex->getMessage());
            }
        }

        return $this->render('user/update.html.twig', [
            "userForm" => $userForm->createView(),
            "userPasswordForm" => $userPasswordForm->createView(),
        ]);
    }

    /**
     * Show details of another user
     * @Route("/user/detail/{pseudo}", name="user_by_pseudo", methods={"GET"})
     * @param $pseudo
     * @return Response
     */
    public function userByPseudo($pseudo) {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $pseudo]);

        return $this->render('user/user.html.twig',[
            'user' => $user,
        ]);
    }
}
