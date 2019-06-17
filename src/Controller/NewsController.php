<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/news")
 */
class NewsController extends AbstractController
{
    /**
     * @Route("/", defaults={"page": "1"}, name="news_index", methods={"GET"})
     * @Route("/news/{page<[1-9]\d*>}", methods={"GET"}, name="news_paginated")
     */
    public function index(NewsRepository $newsRepository, int $page): Response
    {

        return $this->render('news/index.html.twig', [
            'news' => $newsRepository->findLatest($page, 10),
            'activemenu' => 'news',
        ]);
    }

    /**
     * @Route("/new", name="news_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($news);
            $entityManager->flush();

            return $this->redirectToRoute('news_edit', [
                'id' => $news->getId(),
            ]);
        }

        return $this->render('news/new.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
            'activemenu' => 'news',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="news_edit", methods={"GET","POST"})
     * @param Request $request
     * @param News $news
     * @return Response
     */
    public function edit(Request $request, News $news): Response
    {
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('news_edit', [
                'id' => $news->getId(),
            ]);
        }

        return $this->render('news/edit.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
            'activemenu' => 'news',
        ]);
    }

    /**
     * @Route("/{id}", name="news_delete", methods={"DELETE"})
     */
    public function delete(Request $request, News $news): Response
    {
        if ($this->isCsrfTokenValid('delete'.$news->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($news);
            $entityManager->flush();
        }

        return $this->redirectToRoute('news_index');
    }
}
