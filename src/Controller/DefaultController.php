<?php

namespace App\Controller;

use App\Form\ContactType;
use Maith\Common\AdminBundle\Services\MaithParametersService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\NewsRepository;
use Maith\Common\AdminBundle\Repository\mAlbumRepository;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @Route("/contacto.html", name="contactForm")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @param MaithParametersService $maithParametersService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contactForm(Request $request, \Swift_Mailer $mailer, MaithParametersService $maithParametersService)
    {
        $form = $this->createForm(ContactType::class,null,array(
            // To set the action use $this->generateUrl('route_identifier')
            'action' => $this->generateUrl('contactForm'),
            'method' => 'POST'
        ));
        $message = null;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (empty($data['lastname'])) {
                $this->sendEmail($maithParametersService, $mailer, $data['name'], $data['subject'], $data['email'], $data['message']);
                $message = "Mensaje enviado correctamente";
            } else {
                $message = "Ocurrio un error al enviar el mail.";
            }
        }
        return $this->render('default/contact.html.twig', [
            'controller_name' => 'DefaultController',
            'menu' => 'contact',
            'form' => $form->createView(),
            'message' => $message,
        ]);
    }

    /**
     * @param MaithParametersService $maithParametersService
     * @param \Swift_Mailer $mailer
     * @param $name
     * @param $subject
     * @param $email
     * @param $message
     * @return int
     */
    private function sendEmail(MaithParametersService $maithParametersService, \Swift_Mailer $mailer, $name, $subject, $email, $message)
    {
        $from = [$maithParametersService->getParameter('contact-email-from') => $maithParametersService->getParameter('contact-email-from-name')];
        $message = (new \Swift_Message($maithParametersService->getParameter('contact-email-subject')))
            ->setFrom($from)
            ->setTo($maithParametersService->getParameter('contact-email-to'))
            ->setReplyTo($email)
            ->setBody(
                $this->renderView(
                    'emails/contact.html.twig',
                    [
                        'name' => $name,
                        'subject' => $subject,
                        'email' => $email,
                        'message' => $message,
                    ]
                ),
                'text/html'
            )
            /*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'emails/registration.txt.twig',
                    ['name' => $name]
                ),
                'text/plain'
            )
            */
        ;

        return $mailer->send($message);
    }
}
