<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Wrapper tipis di atas Dompdf. Merender sebuah view HTML menjadi PDF dan
 * mengembalikannya sebagai Response siap diunduh. Dipakai oleh semua
 * controller laporan (Fitur §4.6).
 */
class PdfExport
{
    /**
     * @param string $viewName    Nama view (tanpa .php) yang berisi markup laporan.
     * @param array  $data        Data yang dikirim ke view.
     * @param string $orientasi   'portrait' atau 'landscape'.
     */
    public function unduh(string $viewName, array $data, string $namaFile, string $orientasi = 'portrait')
    {
        helper('simpkl');

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view($viewName, $data));
        $dompdf->setPaper('A4', $orientasi);
        $dompdf->render();

        return service('response')
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $namaFile . '"')
            ->setBody($dompdf->output());
    }
}
