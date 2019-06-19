<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\NewsRepository;
use Maith\Common\AdminBundle\Repository\mAlbumRepository;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'menu' => 'inicio'
        ]);
    }

    /**
     * @Route("/noticias", defaults={"page": "1"}, name="newslist")
     * @Route("/noticias/{page<[1-9]\d*>}", methods={"GET"}, name="newslist_paginated")
     * @param NewsRepository $newsRepository
     * @param mAlbumRepository $albumRepository
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newsList(NewsRepository $newsRepository, mAlbumRepository $albumRepository, int $page)
    {
        $data = $newsRepository->findLatest($page, 10);
        $mediaData = [];
        foreach($data as $news)
        {
            $avatar = $albumRepository->retrieveFirstFileOfAlbum($news->getId(), $news->getFullClassName(), 'principal');
            if ($avatar) {
                $mediaData[$news->getId()] = $avatar;
            }
        }
        return $this->render('default/newslist.html.twig', [
            'controller_name' => 'DefaultController',
            'menu' => 'news',
            'newslist' => $data,
            'mediaData' => $mediaData
        ]);
    }

    /**
     * @Route("/noticia/{slug}.html", name="shownew")
     * @param NewsRepository $newsRepository
     * @param mAlbumRepository $albumRepository
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showNews(NewsRepository $newsRepository, mAlbumRepository $albumRepository, $slug)
    {
        $object = $newsRepository->findOneBySlug($slug);
        /** @var \Maith\Common\AdminBundle\Entity\mAlbum $album */
        $album = $albumRepository->retrieveByObjectIdClassAndAlbumName($object->getId(), $object->getFullClassName(), 'principal');
        $avatar = null;
        $first = true;
        $files = [];
        if ($album) {
            foreach ($album->getFiles() as $file)
            {
                if ($first) {
                    $avatar = $file;
                    $first = false;
                } else {
                    $files[] = $file;
                }
            }
        }
        return $this->render('default/news.html.twig', [
            'controller_name' => 'DefaultController',
            'menu' => 'news',
            'object' => $object,
            'avatar' => $avatar,
            'files' => $files,
        ]);
    }
}
