<?php

namespace App\Controller;

use App\Entity\SubFee;
use App\Repository\SubFeeRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/sub-fee")
 */
class SubFeeController extends AbstractController
{
    /**
     * @Route("/", defaults={"page": "1"}, name="sub_fee_index", methods={"GET"})
     * @Route("/pager/{page<[1-9]\d*>}", methods={"GET"}, name="sub_fee_index_paginated")
     * @param SubFeeRepository $subFeeRepository
     * @param int $page
     * @return Response
     */
    public function index(SubFeeRepository $subFeeRepository, int $page): Response
    {
        return $this->render('sub_fee/index.html.twig', [
            'sub_fees' => $subFeeRepository->findLatest($page, 10),
        ]);
    }

    /**
     * @Route("/show/{id}", name="sub_fee_show", methods={"GET"})
     * @param SubFee $subFee
     * @return Response
     */
    public function show(SubFee $subFee): Response
    {
        return $this->render('sub_fee/show.html.twig', [
            'sub_fee' => $subFee,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="sub_fee_delete", methods={"DELETE"})
     * @param Request $request
     * @param SubFee $subFee
     * @return Response
     */
    public function delete(Request $request, SubFee $subFee): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subFee->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($subFee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sub_fee_index');
    }

    /**
     * @Route("/approve/{id}", name="sub_fee_approved", methods={"PUT"})
     * @param Request $request
     * @param SubFee $subFee
     * @return Response
     */
    public function changeApproved(Request $request, SubFee $subFee): Response
    {
        if ($this->isCsrfTokenValid('approved'.$subFee->getId(), $request->request->get('_token'))) {
            $subFee->setApproved(!$subFee->getApproved());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subFee);
            $entityManager->flush();
        }
        return $this->redirectToRoute('sub_fee_show', ['id' => $subFee->getId()]);
    }

    /**
     * @Route("/download/{id}.html", name="sub_fee_download", methods={"GET"})
     * @param SubFee $subFee
     * @return BinaryFileResponse
     */
    public function download(SubFee $subFee): BinaryFileResponse
    {
        // This should return the file to the browser as response
        $response = new BinaryFileResponse($subFee->getPayment());

        // To generate a file download, you need the mimetype of the file
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

        // Set the mimetype with the guesser or manually
        if($mimeTypeGuesser->isGuesserSupported()){
            // Guess the mimetype of the file according to the extension of the file
            $response->headers->set('Content-Type', $mimeTypeGuesser->guessMimeType($subFee->getPayment()));
        }else{
            // Set the mimetype of the file manually, in this case for a text file is text/plain
            $response->headers->set('Content-Type', 'text/plain');
        }

        // Set content disposition inline of the file
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            basename($subFee->getPayment())
        );

        return $response;
    }

    /**
     * @Route("/export.html", name="sub_fee_export", methods={"GET"})
     * @param SubFeeRepository $subFeeRepository
     * @return BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportData(SubFeeRepository $subFeeRepository)
    {
        $spreadsheet = new Spreadsheet();

        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Inscripciones");
        $position = 1;
        $sheet->setCellValueByColumnAndRow(1, $position, 'Id');
        $sheet->setCellValueByColumnAndRow(2, $position, 'Nombre');
        $sheet->setCellValueByColumnAndRow(3, $position, 'Cedula');
        $sheet->setCellValueByColumnAndRow(4, $position, 'Email');
        $sheet->setCellValueByColumnAndRow(5, $position, 'Aprovado');
        $sheet->setCellValueByColumnAndRow(6, $position, 'Lugar donde esta el archivo');
        $position++;
        /** @var SubFee $subFee */
        foreach ($subFeeRepository->findAll() as $subFee) {
            $sheet->setCellValueByColumnAndRow(1, $position, $subFee->getId());
            $sheet->setCellValueByColumnAndRow(2, $position, $subFee->getName());
            $sheet->setCellValueByColumnAndRow(3, $position, $subFee->getIdentity());
            $sheet->setCellValueByColumnAndRow(4, $position, $subFee->getEmail());
            $sheet->setCellValueByColumnAndRow(5, $position, ($subFee->getApproved()) ? "Si": "No");
            $sheet->setCellValueByColumnAndRow(6, $position, $subFee->getPayment());
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
}
