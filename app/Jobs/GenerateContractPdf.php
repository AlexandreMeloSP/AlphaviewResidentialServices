<?php

namespace App\Jobs;

use App\Models\Contract;
use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateContractPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        public Contract $contrato,
    ) {
    }

    public function handle(PdfService $pdfService): void
    {
        try {
            $path = $pdfService->generateContractPdf($this->contrato);

            Log::info('PDF do contrato gerado com sucesso.', [
                'contrato_id' => $this->contrato->id,
                'path' => $path,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF do contrato.', [
                'contrato_id' => $this->contrato->id,
                'erro' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
