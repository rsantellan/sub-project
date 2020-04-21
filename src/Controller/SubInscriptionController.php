<?php

namespace App\Controller;

use App\Entity\SubInscription;
use App\Repository\SubInscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Import required classes
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use \Symfony\Component\Mime\FileinfoMimeTypeGuesser;

// Include PhpSpreadsheet required namespaces
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * @Route("/admin/inscription")
 */
class SubInscriptionController extends AbstractController
{

    /**
     * @Route("/", defaults={"page": "1"}, name="sub_inscription_index", methods={"GET"})
     * @Route("/pager/{page<[1-9]\d*>}", methods={"GET"}, name="sub_inscription_index_paginated")
     * @param SubInscriptionRepository $subInscriptionRepository
     * @param int $page
     * @return Response
     */
    public function index(SubInscriptionRepository $subInscriptionRepository, int $page): Response
    {
        return $this->render('sub_inscription/index.html.twig', [
            'sub_inscriptions' => $subInscriptionRepository->findLatest($page, 10),
        ]);
    }

    /**
     * @Route("/export.html", name="sub_inscription_export", methods={"GET"})
     * @param SubInscriptionRepository $subInscriptionRepository
     * @return BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportData(SubInscriptionRepository $subInscriptionRepository)
    {
        $spreadsheet = new Spreadsheet();

        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Inscripciones");
        $position = 1;
        $sheet->setCellValueByColumnAndRow(1, $position, 'Id');
        $sheet->setCellValueByColumnAndRow(2, $position, 'Nombre');
        $sheet->setCellValueByColumnAndRow(3, $position, 'Cedula');
        $sheet->setCellValueByColumnAndRow(4, $position, 'Dirección');
        $sheet->setCellValueByColumnAndRow(5, $position, 'Email');
        $sheet->setCellValueByColumnAndRow(6, $position, 'Nivel');
        $sheet->setCellValueByColumnAndRow(7, $position, 'Fecha');
        $sheet->setCellValueByColumnAndRow(8, $position, 'Secciones');
        $sheet->setCellValueByColumnAndRow(9, $position, 'Aprobado');
        $sheet->setCellValueByColumnAndRow(10, $position, 'Nombre del archivo');
        $position++;
        /** @var SubInscription $subInscription */
        foreach ($subInscriptionRepository->findAll() as $subInscription) {
            $seccionesLabel = [];
            foreach ($subInscription->getSections() as $section) {
                $seccionesLabel[] = $section->getName();
            }
            $sheet->setCellValueByColumnAndRow(1, $position, $subInscription->getId());
            $sheet->setCellValueByColumnAndRow(2, $position, $subInscription->getName());
            $sheet->setCellValueByColumnAndRow(3, $position, $subInscription->getIdentity());
            $sheet->setCellValueByColumnAndRow(4, $position, $subInscription->getAddress());
            $sheet->setCellValueByColumnAndRow(5, $position, $subInscription->getEmail());
            $sheet->setCellValueByColumnAndRow(6, $position, $subInscription->getLevelAsString());
            $sheet->setCellValueByColumnAndRow(7, $position, $subInscription->getStartDate()->format('d/m/Y'));
            $sheet->setCellValueByColumnAndRow(8, $position, implode(',', $seccionesLabel));
            $sheet->setCellValueByColumnAndRow(9, $position, ($subInscription->getApproved()) ? "Si": "No");
            $sheet->setCellValueByColumnAndRow(10, $position, $subInscription->getPayment());
            $position++;
        }



        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);

        // Create a Temporary file in the system
        $fileName = 'inscripciones.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);

        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/show/{id}", name="sub_inscription_show", methods={"GET"})
     * @param SubInscription $subInscription
     * @return Response
     */
    public function show(SubInscription $subInscription): Response
    {
        return $this->render('sub_inscription/show.html.twig', [
            'sub_inscription' => $subInscription,
        ]);
    }

    /**
     * @Route("/download/{id}.html", name="sub_inscription_download", methods={"GET"})
     */
    public function download(SubInscription $subInscription): BinaryFileResponse
    {
        // This should return the file to the browser as response
        $response = new BinaryFileResponse($subInscription->getPayment());

        // To generate a file download, you need the mimetype of the file
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

        // Set the mimetype with the guesser or manually
        if($mimeTypeGuesser->isGuesserSupported()){
            // Guess the mimetype of the file according to the extension of the file
            $response->headers->set('Content-Type', $mimeTypeGuesser->guessMimeType($subInscription->getPayment()));
        }else{
            // Set the mimetype of the file manually, in this case for a text file is text/plain
            $response->headers->set('Content-Type', 'text/plain');
        }

        // Set content disposition inline of the file
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            basename($subInscription->getPayment())
        );

        return $response;
    }

    /**
     * @Route("/approve/{id}", name="sub_inscription_approved", methods={"PUT"})
     * @param Request $request
     * @param SubInscription $subInscription
     * @return Response
     */
    public function changeApproved(Request $request, SubInscription $subInscription): Response
    {
        if ($this->isCsrfTokenValid('approved'.$subInscription->getId(), $request->request->get('_token'))) {
            $subInscription->setApproved(!$subInscription->getApproved());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subInscription);
            $entityManager->flush();
        }
        return $this->redirectToRoute('sub_inscription_show', ['id' => $subInscription->getId()]);
    }

    /**
     * @Route("/delete/{id}", name="sub_inscription_delete", methods={"DELETE"})
     * @param Request $request
     * @param SubInscription $subInscription
     * @return Response
     */
    public function delete(Request $request, SubInscription $subInscription): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subInscription->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($subInscription);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sub_inscription_index');
    }


}
