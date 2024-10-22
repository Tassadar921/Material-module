<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class PDFService
{

    public function __construct(
        private readonly Environment $twig,
    )
    { }

    public function generatePdf($htmlContent)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4',);
        $dompdf->render();

        return $dompdf->output();
    }
}
