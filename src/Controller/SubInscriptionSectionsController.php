<?php

namespace App\Controller;

use App\Entity\SubInscriptionSections;
use App\Form\SubInscriptionSectionsType;
use App\Repository\SubInscriptionSectionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/sub/inscription-sections")
 */
class SubInscriptionSectionsController extends AbstractController
{
    /**
     * @Route("/", name="sub_inscription_sections_index", methods={"GET"})
     */
    public function index(SubInscriptionSectionsRepository $subInscriptionSectionsRepository): Response
    {
        return $this->render('sub_inscription_sections/index.html.twig', [
            'sub_inscription_sections' => $subInscriptionSectionsRepository->findAll(),
            'activemenu' => 'sections-inscription',
        ]);
    }

    /**
     * @Route("/new", name="sub_inscription_sections_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $subInscriptionSection = new SubInscriptionSections();
        $form = $this->createForm(SubInscriptionSectionsType::class, $subInscriptionSection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subInscriptionSection);
            $entityManager->flush();

            return $this->redirectToRoute('sub_inscription_sections_index');
        }

        return $this->render('sub_inscription_sections/new.html.twig', [
            'sub_inscription_section' => $subInscriptionSection,
            'form' => $form->createView(),
            'activemenu' => 'sections-inscription',
        ]);
    }

    /**
     * @Route("/{id}", name="sub_inscription_sections_show", methods={"GET"})
     */
    public function show(SubInscriptionSections $subInscriptionSection): Response
    {
        return $this->render('sub_inscription_sections/show.html.twig', [
            'sub_inscription_section' => $subInscriptionSection,
            'activemenu' => 'sections-inscription',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sub_inscription_sections_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SubInscriptionSections $subInscriptionSection): Response
    {
        $form = $this->createForm(SubInscriptionSectionsType::class, $subInscriptionSection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sub_inscription_sections_index', [
                'id' => $subInscriptionSection->getId(),
            ]);
        }

        return $this->render('sub_inscription_sections/edit.html.twig', [
            'sub_inscription_section' => $subInscriptionSection,
            'form' => $form->createView(),
            'activemenu' => 'sections-inscription',
        ]);
    }

    /**
     * @Route("/{id}", name="sub_inscription_sections_delete", methods={"DELETE"})
     */
    public function delete(Request $request, SubInscriptionSections $subInscriptionSection): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subInscriptionSection->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($subInscriptionSection);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sub_inscription_sections_index');
    }
}
