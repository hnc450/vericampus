<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Durée de validité par défaut d'un document de vérification
    |--------------------------------------------------------------------------
    | En jours, à partir de la génération. `null` = pas d'expiration automatique
    | (le document reste valide jusqu'à révocation explicite par l'université).
    */
    'document_ttl_days' => env('VERIFICATION_DOCUMENT_TTL_DAYS') ? (int) env('VERIFICATION_DOCUMENT_TTL_DAYS') : null,

];
