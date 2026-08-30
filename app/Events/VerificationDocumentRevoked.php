<?php

namespace App\Events;

use App\Models\VerificationDocument;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VerificationDocumentRevoked
{
    use Dispatchable, SerializesModels;

    public function __construct(public VerificationDocument $document) {}
}
