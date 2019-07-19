<?php

namespace App\Controller;

use App\Entity\SubSections;
use App\Form\SubSectionsType;
use App\Repository\SubSectionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/admin/sub/sections")
 */
class SubSectionsController extends AbstractController
{
    /**
     * @Route("/", defaults={"page": "1"}, name="sub_sections_index", methods={"GET"})
     * @Route("/sections/{page<[1-9]\d*>}", methods={"GET"}, name="sub_sections_paginated")
     * @param SubSectionsRepository $subSectionsRepository
     * @param int $page
     * @return Response
     */
    public function index(SubSectionsRepository $subSectionsRepository, int $page): Response
    {
        return $this->render('sub_sections/index.html.twig', [
            'sub_sections' => $subSectionsRepository->findLatest($page, 10),
        ]);
    }

    /**
     * @Route("/new", name="sub_sections_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $subSection = new SubSections();
        $form = $this->createForm(SubSectionsType::class, $subSection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subSection);
            $entityManager->flush();

            return $this->redirectToRoute('sub_sections_edit', ['id' => $subSection->getId()]);
        }

        return $this->render('sub_sections/new.html.twig', [
            'sub_section' => $subSection,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sub_sections_show", methods={"GET"})
     */
    public function show(SubSections $subSection): Response
    {
        return $this->render('sub_sections/show.html.twig', [
            'sub_section' => $subSection,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sub_sections_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SubSections $subSection): Response
    {
        $form = $this->createForm(SubSectionsType::class, $subSection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sub_sections_edit', [
                'id' => $subSection->getId(),
            ]);
        }

        return $this->render('sub_sections/edit.html.twig', [
            'sub_section' => $subSection,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sub_sections_delete", methods={"DELETE"})
     */
    public function delete(Request $request, SubSections $subSection): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subSection->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($subSection);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sub_sections_index');
    }

    /**
     * @Route("/sort/show", name="sub_sections_sort", methods={"GET"})
     * @param SubSectionsRepository $subSectionsRepository
     * @return Response
     */
    public function showSortWindow(SubSectionsRepository $subSectionsRepository) : Response
    {
        $params = [
            'sub_sections' => $subSectionsRepository->findAllByPosition(),
        ];
        return $this->render('sub_sections/sortable.html.twig', $params);
    }

    /**
     * @Route("/sort/do", name="sub_sections_do_sort", methods={"POST"})
     * @param Request $request
     * @param SubSectionsRepository $subSectionsRepository
     * @return Response
     */
    public function doSort(Request $request, SubSectionsRepository $subSectionsRepository) : Response
    {
        $type = $request->request->get("album_id");
        $items = $request->request->get("listItem");

        $subSections = $subSectionsRepository->findAllByPosition();
        $entityManager = $this->getDoctrine()->getManager();
        foreach($subSections as $section) {
            $found = false;
            $counter = 0;
            while (!$found && $counter < count($items)) {
                $identity = (int)$items[$counter];
                if ($identity == $section->getId()) {
                    $section->setPosition($counter);
                    $entityManager->persist($section);
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
