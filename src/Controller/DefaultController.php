<?php

namespace App\Controller;

use App\Entity\SubAuthority;
use App\Entity\SubInscription;
use App\Form\BecaMovilidadType;
use App\Form\ContactType;
use App\Form\SubInscriptionType;
use App\Repository\SubAuthorityRepository;
use App\Repository\SubSectionsRepository;
use Maith\Common\AdminBundle\Services\MaithParametersService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\NewsRepository;
use Maith\Common\AdminBundle\Repository\mAlbumRepository;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @Route("/", name="homepage")
     * @param NewsRepository $newsRepository
     * @param mAlbumRepository $albumRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(NewsRepository $newsRepository, mAlbumRepository $albumRepository)
    {
        $newsPageQuantity = 3;
        $mediaData = [];
        $dataPage1 = $newsRepository->findLatest(1, $newsPageQuantity);
        $firstNew = null;
        foreach ($dataPage1 as $news) {
            if ($firstNew === null) {
                $firstNew = $news;
            }
            $avatar = $albumRepository->retrieveFirstFileOfAlbum($news->getId(), $news->getFullClassName(), 'principal');
            if ($avatar) {
                $mediaData[$news->getId()] = $avatar;
                $firstNew = $news;
            }
        }
        $secondNew = null;
        if ($dataPage1->hasNextPage()) {
            $dataPage2 = $newsRepository->findLatest(2, $newsPageQuantity);
        } else {
            $dataPage2 = [];
        }

        foreach ($dataPage2 as $news) {
            if ($secondNew === null) {
                $secondNew = $news;
            }
            $avatar = $albumRepository->retrieveFirstFileOfAlbum($news->getId(), $news->getFullClassName(), 'principal');
            if ($avatar) {
                $mediaData[$news->getId()] = $avatar;
                $secondNew = $news;
            }
        }
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'menu' => 'inicio',
            'newsMedia' => $mediaData,
            'news' => [
                1 => $dataPage1,
                2 => $dataPage2,
                'first' => $firstNew,
                'second' => $secondNew,
            ]
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
        foreach ($data as $news) {
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
            foreach ($album->getFiles() as $file) {
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
        $form = $this->createForm(ContactType::class, null, array(
            // To set the action use $this->generateUrl('route_identifier')
            'action' => $this->generateUrl('contactForm'),
            'method' => 'POST'
        ));
        $message = null;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (empty($data['lastname'])) {
                $this->sendContactEmail($maithParametersService, $mailer, $data['name'], $data['subject'], $data['email'], $data['message']);
                $message = "Mensaje enviado correctamente";
            } else {
                $message = "Ocurrio un error al enviar el mail. Parametros incorrectos";
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
    private function sendContactEmail(MaithParametersService $maithParametersService, \Swift_Mailer $mailer, $name, $subject, $email, $message)
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
        ;

        return $mailer->send($message);
    }

    /**
     * @Route("/autoridades.html", name="site_authorities")
     * @param SubAuthorityRepository $subAuthorityRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAuthorities(SubAuthorityRepository $subAuthorityRepository)
    {
        $returnData = [
            'titulares' => $subAuthorityRepository->getByType(SubAuthority::TITULAR),
            'suplentes' => $subAuthorityRepository->getByType(SubAuthority::SUPLENTE),
            'comision' => $subAuthorityRepository->getByType(SubAuthority::COMISION),
        ];
        return $this->render('default/authorities.html.twig', [
            'controller_name' => 'DefaultController',
            'menu' => 'authorities',
            'data' => $returnData
        ]);
    }

    /**
     * @Route("/becas.html", name="site_becas")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function becas()
    {
        return $this->render('default/becas.html.twig', [
            'controller_name' => 'DefaultController',
            'menu' => 'becas',
        ]);
    }

    /**
     * @Route("/becas-congresos.html", name="site_becas_congresos")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function becasCongresos(Request $request, \Swift_Mailer $mailer, MaithParametersService $maithParametersService)
    {
        $message = null;
        $form = $this->createForm(BecaMovilidadType::class, null, array(
            // To set the action use $this->generateUrl('route_identifier')
            'action' => $this->generateUrl('site_becas_congresos'),
            'method' => 'POST'
        ));
        $message = null;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (empty($data['lastname'])) {
                $this->sendBecaEmail($maithParametersService, $mailer, $data, 'emails/becasCongresos.html.twig');
                //$this->sendEmail($maithParametersService, $mailer, $data['name'], $data['subject'], $data['email'], $data['message']);
                $message = "Mensaje enviado correctamente";
            } else {
                $message = "Ocurrio un error al enviar el mail. Parametros incorrectos";
            }
        }
        return $this->render('default/becasCongreso.html.twig', [
            'controller_name' => 'DefaultController',
            'menu' => 'becas',
            'form' => $form->createView(),
            'message' => $message,
        ]);
    }

    /**
     * @Route("/becas-movilidad.html", name="site_becas_movilidad")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function becasMovilidad(Request $request, \Swift_Mailer $mailer, MaithParametersService $maithParametersService)
    {

        $message = null;
        $form = $this->createForm(BecaMovilidadType::class, null, array(
            // To set the action use $this->generateUrl('route_identifier')
            'action' => $this->generateUrl('site_becas_movilidad'),
            'method' => 'POST'
        ));
        $message = null;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (empty($data['lastname'])) {
                $this->sendBecaEmail($maithParametersService, $mailer, $data, 'emails/becasMovilidad.html.twig');
                //$this->sendEmail($maithParametersService, $mailer, $data['name'], $data['subject'], $data['email'], $data['message']);
                $message = "Mensaje enviado correctamente";
            } else {
                $message = "Ocurrio un error al enviar el mail. Parametros incorrectos";
            }
        }
        return $this->render('default/becasMovilidad.html.twig', [
            'controller_name' => 'DefaultController',
            'menu' => 'becas',
            'form' => $form->createView(),
            'message' => $message,
        ]);
    }

    /**
     * @param MaithParametersService $maithParametersService
     * @param \Swift_Mailer $mailer
     * @param [] $data
     * @return int
     */
    private function sendBecaEmail(MaithParametersService $maithParametersService, \Swift_Mailer $mailer, $data, $viewType = null)
    {
        $email = $data['email'];
        $name = $data['name'];
        $telephone = $data['telephone'];
        $institution = $data['institution'];
        $program = $data['program'];
        $message = $data['message'];
        $from = [$maithParametersService->getParameter('becas-email-from') => $maithParametersService->getParameter('becas-email-from-name')];
        $message = (new \Swift_Message($maithParametersService->getParameter('becas-email-subject')))
            ->setFrom($from)
            ->setTo($maithParametersService->getParameter('becas-email-to'))
            ->setReplyTo($email)
            ->setBody(
                $this->renderView(
                    'emails/becasMovilidad.html.twig',
                    [
                        'name' => $name,
                        'telephone' => $telephone,
                        'email' => $email,
                        'message' => $message,
                        'institution' => $institution,
                        'program' => $program,
                    ]
                ),
                'text/html'
            )
        ;
        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $fileUploaded */
        foreach($data['cv'] as $fileUploaded) {
            $attachment = \Swift_Attachment::fromPath($fileUploaded->getPath());
            $attachment->setFilename($fileUploaded->getClientOriginalName());
            $message->attach($attachment);
        }

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $fileUploaded */
        foreach($data['scholarship'] as $fileUploaded) {
            $attachment = \Swift_Attachment::fromPath($fileUploaded->getPath());
            $attachment->setFilename($fileUploaded->getClientOriginalName());
            $message->attach($attachment);
        }

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $fileUploaded */
        foreach($data['letter'] as $fileUploaded) {
            $attachment = \Swift_Attachment::fromPath($fileUploaded->getPath());
            $attachment->setFilename($fileUploaded->getClientOriginalName());
            $message->attach($attachment);
        }
        return $mailer->send($message);
    }

    /**
     * @Route("/seccionales.html", name="site_seccionales")
     * @param SubSectionsRepository $subSectionsRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showSeccionales(SubSectionsRepository $subSectionsRepository)
    {
        return $this->render('default/seccionales.html.twig', [
            'controller_name' => 'DefaultController',
            'menu' => 'seccionales',
            'sub_sections' => $subSectionsRepository->findAllByPosition()
        ]);
    }

    /**
     * @Route("/socios.html", name="site_home_socios")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showHomeSocios()
    {
        return $this->render('default/homeSocios.html.twig', [
            'controller_name' => 'DefaultController',
            'menu' => 'socios',
        ]);
    }


    /**
     * @Route("/socios/inscripcion.html", name="site_socios_inscripcion")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sociosInscripcion(Request $request, \Swift_Mailer $mailer, MaithParametersService $maithParametersService)
    {
        $inscription = new SubInscription();
        $message = null;
        $form = $this->createForm(SubInscriptionType::class, $inscription, array(
            // To set the action use $this->generateUrl('route_identifier')
            'action' => $this->generateUrl('site_socios_inscripcion'),
            'method' => 'POST'
        ));
        $message = null;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $lastname = $form->get('lastname')->getData();
            if (empty($lastname)) {

                $file = $form->get('payment')->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($inscription);
                $entityManager->flush();
                $projectDir = $this->parameterBag->get('kernel.project_dir');
                $dirName = $projectDir.DIRECTORY_SEPARATOR.'inscripciones'.DIRECTORY_SEPARATOR;
                if (!file_exists($dirName)) {
                    mkdir($dirName, 0777);
                }
                $file->move($dirName, $inscription->getId(). ' - '.$file->getClientOriginalName());
                $inscription->setPayment($dirName.$inscription->getId(). ' - '.$file->getClientOriginalName());
                $entityManager->persist($inscription);
                $entityManager->flush();
                //$this->sendBecaEmail($maithParametersService, $mailer, $data, 'emails/becasMovilidad.html.twig');
                //$this->sendEmail($maithParametersService, $mailer, $data['name'], $data['subject'], $data['email'], $data['message']);
                $message = "Mensaje enviado correctamente";
            } else {
                $message = "Ocurrio un error al enviar el mail. Parametros incorrectos";
            }
        }
        return $this->render('default/sociosInscripcion.html.twig', [
            'controller_name' => 'DefaultController',
            'menu' => 'socios',
            'form' => $form->createView(),
            'message' => $message,
        ]);
    }
}
