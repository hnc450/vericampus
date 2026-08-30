<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vérification de document — VeriCampus</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-6">
            <div class="text-lg font-semibold">VeriCampus</div>
            <div class="text-xs text-slate-500">Vérification de document académique</div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-8 text-center">
            @if(! $found)
                <div class="text-3xl mb-3">✕</div>
                <div class="text-lg font-semibold text-red-700 mb-1">DOCUMENT NON VALIDE</div>
                <p class="text-sm text-slate-500">Aucun document ne correspond à ce code. Il est possible que le lien soit incorrect ou falsifié.</p>
            @elseif($status === 'verified')
                <div class="text-3xl mb-3">✓</div>
                <div class="text-lg font-semibold text-emerald-700 mb-4">DOCUMENT AUTHENTIQUE</div>
                <div class="text-left text-sm space-y-2">
                    <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-500">Candidat</span><span class="font-medium">{{ $document->student->fullName() }}</span></div>
                    <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-500">Université</span><span class="font-medium">{{ $document->university->name }}</span></div>
                    <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-500">Faculté</span><span class="font-medium">{{ $document->faculty->name }}</span></div>
                    <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-500">Année académique</span><span class="font-medium">{{ $document->academicYear->label }}</span></div>
                    <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-500">ID du document</span><span class="font-mono text-xs">{{ $document->document_uid }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Date de vérification</span><span class="font-medium">{{ now()->format('d/m/Y à H:i') }}</span></div>
                </div>
            @elseif($status === 'revoked')
                <div class="text-3xl mb-3">✕</div>
                <div class="text-lg font-semibold text-red-700 mb-1">DOCUMENT RÉVOQUÉ</div>
                <p class="text-sm text-slate-500">Ce document a été révoqué par l'université émettrice{{ $document->revoked_at ? ' le '.$document->revoked_at->format('d/m/Y') : '' }} et n'est plus valide.</p>
            @elseif($status === 'expired')
                <div class="text-3xl mb-3">✕</div>
                <div class="text-lg font-semibold text-amber-700 mb-1">DOCUMENT EXPIRÉ</div>
                <p class="text-sm text-slate-500">La période de validité de ce document est dépassée.</p>
            @endif
        </div>

        <p class="text-xs text-slate-400 text-center mt-6">
            Cette page confirme uniquement l'authenticité du document — elle ne remplace pas une vérification directe auprès de l'université pour toute décision officielle.
        </p>
    </div>
</body>
</html>
