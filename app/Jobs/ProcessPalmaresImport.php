<?php

namespace App\Jobs;

use App\Events\PalmaresImported;
use App\Models\PalmaresImport;
use App\Services\PalmaresImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPalmaresImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(public PalmaresImport $import) {}

    public function handle(PalmaresImportService $service): void
    {
        $service->process($this->import);

        PalmaresImported::dispatch($this->import->fresh());
    }
}
