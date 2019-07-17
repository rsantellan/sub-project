<?php

namespace App\Controller;

use App\Entity\SubAuthority;
use App\Form\SubAuthorityType;
use App\Repository\SubAuthorityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/authority")
 */
class SubAuthorityController extends AbstractController
{
    /**
     * @Route("/", defaults={"page": "1"}, name="sub_authority_index", methods={"GET"})
     * @Route("/authorities/{page<[1-9]\d*>}", methods={"GET"}, name="sub_authority_paginated")
     * @param SubAuthorityRepository $subAuthorityRepository
     * @param int $page
     * @return Response
     */
    public function index(SubAuthorityRepository $subAuthorityRepository, int $page): Response
    {
        return $this->render('sub_authority/index.html.twig', [
            'sub_authorities' => $subAuthorityRepository->findLatest($page, 10),
            'activemenu' => 'authorities',
        ]);
    }

    /**
     * @Route("/new", name="sub_authority_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $subAuthority = new SubAuthority();
        $form = $this->createForm(SubAuthorityType::class, $subAuthority);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subAuthority);
            $entityManager->flush();

            return $this->redirectToRoute('sub_authority_edit', [
                'id' => $subAuthority->getId(),
            ]);
        }

        return $this->render('sub_authority/new.html.twig', [
            'sub_authority' => $subAuthority,
            'form' => $form->createView(),
            'activemenu' => 'authorities',
        ]);
    }

    /**
     * @Route("/{id}", name="sub_authority_show", methods={"GET"})
     */
    public function show(SubAuthority $subAuthority): Response
    {
        return $this->render('sub_authority/show.html.twig', [
            'sub_authority' => $subAuthority,
            'activemenu' => 'authorities',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sub_authority_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SubAuthority $subAuthority): Response
    {
        $form = $this->createForm(SubAuthorityType::class, $subAuthority);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sub_authority_edit', [
                'id' => $subAuthority->getId(),
            ]);
        }

        return $this->render('sub_authority/edit.html.twig', [
            'sub_authority' => $subAuthority,
            'form' => $form->createView(),
            'activemenu' => 'authorities',
        ]);
    }

    /**
     * @Route("/{id}", name="sub_authority_delete", methods={"DELETE"})
     */
    public function delete(Request $request, SubAuthority $subAuthority): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subAuthority->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($subAuthority);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sub_authority_index');
    }

    /**
     * @Route("/sort/{type<[1-9]\d*>}", name="sub_authority_sort", methods={"GET"})
     * @param SubAuthorityRepository $subAuthorityRepository
     * @param int $type
     * @return Response
     */
    public function showSortWindow(SubAuthorityRepository $subAuthorityRepository,  int $type) : Response
    {
        $params = [
            'sub_authorities' => $subAuthorityRepository->getByType($type),
            'type' => $type,
            ];
        return $this->render('sub_authority/sortable.html.twig', $params);
    }

    /**
     * @Route("/do-sort", name="sub_authority_do_sort", methods={"POST"})
     * @param Request $request
     * @param SubAuthorityRepository $subAuthorityRepository
     * @param int $type
     * @return Response
     */
    public function doSort(Request $request, SubAuthorityRepository $subAuthorityRepository) : Response
    {
        $type = $request->request->get("album_id");
        $items = $request->request->get("listItem");

        $subAuthorities = $subAuthorityRepository->getByType($type);
        $entityManager = $this->getDoctrine()->getManager();
        foreach($subAuthorities as $authority) {
            $found = false;
            $counter = 0;
            while (!$found && $counter < count($items)) {
                $identity = (int)$items[$counter];
                if ($identity == $authority->getId()) {
                    $authority->setPosition($counter);
                    $entityManager->persist($authority);
                    $entityManager->flush();
                    $found = true;
                }
                $counter++;
            }
        }
        $response = new JsonResponse();
        $response->setData(array('output' => true));
        return $response;
    }
}
