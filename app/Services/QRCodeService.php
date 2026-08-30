<?php

namespace App\Services;

use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * The QR code embedded in a verification document must never carry personal
 * data — only an opaque, unguessable token that the public /verify page uses
 * to look up a VerificationDocument server-side (cahier des charges §10).
 */
class QRCodeService
{
    public function generateToken(): string
    {
        return Str::random(48);
    }

    public function svgFor(string $qrToken): string
    {
        $url = route('public.verify', ['token' => $qrToken]);

        return QrCode::size(280)
            ->errorCorrection('H')
            ->generate($url);
    }

    public function pngDataUriFor(string $qrToken): string
    {
        $url = route('public.verify', ['token' => $qrToken]);

        $png = QrCode::format('png')
            ->size(280)
            ->errorCorrection('H')
            ->generate($url);

        return 'data:image/png;base64,'.base64_encode($png);
    }
}
