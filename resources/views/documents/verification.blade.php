<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; color: #20242e; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #1f3a5f; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; color: #1f3a5f; margin: 0 0 4px; }
        .header p { margin: 0; color: #565c6c; font-size: 11px; }
        .title { text-align: center; font-size: 15px; font-weight: bold; margin: 18px 0; text-transform: uppercase; letter-spacing: 1px; }
        table.info { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.info td { padding: 6px 8px; border-bottom: 1px solid #d9d2be; }
        table.info td.label { width: 40%; color: #565c6c; font-weight: bold; }
        .footer { margin-top: 30px; display: table; width: 100%; }
        .footer .signature, .footer .qr { display: table-cell; width: 50%; vertical-align: top; text-align: center; }
        .footer img.signature-img { max-height: 60px; }
        .footer .authority { margin-top: 6px; font-size: 11px; }
        .uid { text-align: center; font-size: 10px; color: #7b8194; margin-top: 24px; }
        .status { text-align: center; font-size: 13px; font-weight: bold; color: #2e6b4e; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $document->university->name }}</h1>
        <p>{{ $document->university->city }}{{ $document->university->country ? ', '.$document->university->country : '' }}</p>
    </div>

    <div class="title">Document de vérification académique</div>
    <div class="status">✓ AUTHENTIQUE</div>

    <table class="info">
        <tr><td class="label">Nom du candidat</td><td>{{ $document->student->fullName() }}</td></tr>
        <tr><td class="label">ID étudiant</td><td>{{ $document->student->unique_student_id }}</td></tr>
        <tr><td class="label">Université</td><td>{{ $document->university->name }}</td></tr>
        <tr><td class="label">Faculté</td><td>{{ $document->faculty->name }}</td></tr>
        <tr><td class="label">Année académique</td><td>{{ $document->academicYear->label }}</td></tr>
        @if($document->promotion)
        <tr><td class="label">Promotion</td><td>{{ $document->promotion->name }}</td></tr>
        @endif
        @if($document->degree)
        <tr><td class="label">Diplôme</td><td>{{ $document->degree->name }}</td></tr>
        @endif
        <tr><td class="label">Date de génération</td><td>{{ $document->created_at->format('d/m/Y à H:i') }}</td></tr>
        <tr><td class="label">Identifiant du document</td><td>{{ $document->document_uid }}</td></tr>
    </table>

    <div class="footer">
        <div class="signature">
            @if($signatureImage)
                <img class="signature-img" src="{{ $signatureImage }}" alt="Signature">
                <div class="authority">
                    {{ $document->signature->authority_name }}<br>
                    {{ $document->signature->authority_title }}
                </div>
            @else
                <div class="authority">Signature non disponible</div>
            @endif
        </div>
        <div class="qr">
            <img src="{{ $qrImage }}" width="110" height="110" alt="QR Code de vérification">
            <div class="authority">Scanner pour vérifier en ligne</div>
        </div>
    </div>

    <div class="uid">
        Ce document est vérifiable en ligne à tout moment via son QR Code ou l'identifiant {{ $document->document_uid }}.
        Toute correspondance avec un document falsifié ou modifié invalide automatiquement ce certificat.
    </div>
</body>
</html>
