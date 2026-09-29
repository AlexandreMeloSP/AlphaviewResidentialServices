<?php

namespace App\Services;

use App\Models\Contract;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function generateContractPdf(Contract $contrato): string
    {
        $contrato->load(['exchange.serviceProponente', 'exchange.serviceReceptor', 'user1', 'user2']);

        $data = [
            'contrato' => $contrato,
            'user1' => $contrato->user1,
            'user2' => $contrato->user2,
            'serviceProponente' => $contrato->exchange->serviceProponente,
            'serviceReceptor' => $contrato->exchange->serviceReceptor,
            'dataGeracao' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('pdf.contract', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', false);

        $fileName = 'contrato_' . $contrato->id . '_' . \Illuminate\Support\Str::random(32) . '.pdf';
        $path = 'contratos/' . $fileName;

        $directory = storage_path('app/contratos');
        if (! file_exists($directory)) {
            @mkdir($directory, 0750, true);
        }

        $pdf->save(storage_path('app/' . $path));

        $contrato->forceFill(['pdf_path' => $path])->save();

        return $path;
    }
}
