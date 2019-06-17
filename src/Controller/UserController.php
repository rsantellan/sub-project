<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/users")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", defaults={"page": "1"}, name="admin_user_index", methods={"GET"})
     * @Route("/index/{page<[1-9]\d*>}", methods={"GET"}, name="admin_user_index_paginate")
     * @param UserRepository $userRepository
     * @param int $page
     * @return Response
     */
    public function index(UserRepository $userRepository, int $page) : Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findLatest($page, 20),
            'activemenu' => 'users',
        ]);
    }

    /**
     * @route("/{id<[1-9]\d*>}/show", name="admin_users_show", methods={"GET"})
     * @param User $user
     */
    public function show(User $user)
    {

    }

    /**
     * @route("/{id<[1-9]\d*>}/activate", name="admin_users_activate", methods={"POST"})
     * @param User $user
     */
    public function activate(User $user)
    {

    }

    /**
     * @route("/{id<[1-9]\d*>}/deactivate", name="admin_users_deactivate", methods={"POST"})
     * @param User $user
     */
    public function deactivate(User $user)
    {

    }

    /**
     * @route("/{id<[1-9]\d*>}/edit", name="admin_users_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param User $user
     * @param \FOS\UserBundle\Model\UserManagerInterface $userManager
     * @return Response
     */
    public function edit(Request $request, User $user, \FOS\UserBundle\Model\UserManagerInterface $userManager)
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        }
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'activemenu' => 'user',
        ]);
    }

    /**
     * @route("/new", name="users_new", methods={"GET", "POST"})
     * @param Request $request
     * @param \FOS\UserBundle\Model\UserManagerInterface $userManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function new(Request $request, \FOS\UserBundle\Model\UserManagerInterface $userManager)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $newUser = $userManager->createUser();
            $newUser->setUsername($data['email']);
            $newUser->setEmail($data['email']);
            $newUser->setPlainPassword('sub_admin');
            $newUser->setEnabled(true);
            $newUser->setUserRoles($data['userRoles']->toArray());
            $userManager->updateUser($newUser, false);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_users_edit', [
                'id' => $newUser->getId(),
            ]);
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
            'activemenu' => 'user',
        ]);
    }
}
