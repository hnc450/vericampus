<?php

namespace App\Events;

use App\Models\PalmaresImport;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PalmaresImported
{
    use Dispatchable, SerializesModels;

    public function __construct(public PalmaresImport $import) {}
}
