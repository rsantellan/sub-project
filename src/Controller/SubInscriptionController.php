<?php

namespace App\Controller;

use App\Entity\SubInscription;
use App\Form\SubInscription1Type;
use App\Repository\SubInscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/inscription")
 */
class SubInscriptionController extends AbstractController
{
    /**
     * @Route("/", defaults={"page": "1"}, name="sub_inscription_index", methods={"GET"})
     * @Route("/news/{page<[1-9]\d*>}", methods={"GET"}, name="sub_inscription_index_paginated")
     */
    public function index(SubInscriptionRepository $subInscriptionRepository, int $page): Response
    {
        return $this->render('sub_inscription/index.html.twig', [
            'sub_inscriptions' => $subInscriptionRepository->findLatest($page, 10),
        ]);
    }


    /**
     * @Route("/{id}", name="sub_inscription_show", methods={"GET"})
     */
    public function show(SubInscription $subInscription): Response
    {
        return $this->render('sub_inscription/show.html.twig', [
            'sub_inscription' => $subInscription,
        ]);
    }

}
