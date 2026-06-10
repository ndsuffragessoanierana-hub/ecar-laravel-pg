<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 11px;
            margin: 20px;
        }

        .page-border {
            border: 2px solid #000;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
        }

        .header p {
            margin: 2px;
        }

        .info {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
        }

        th {
            background: #eee;
        }

        .right {
            text-align: right;
        }

        .total {
            font-weight: bold;
            background: #f5f5f5;
        }

        .signature {
            margin-top: 40px;
            width: 100%;
        }

        .signature td {
            border: none;
            text-align: center;
            padding-top: 40px;
        }
    </style>
</head>
<body>

@php
    use Carbon\Carbon;

    // 🔥 Format avec suppression des zéros
    $fmt = function ($v, $hideZero = true) {
        $n = (float) ($v ?? 0);
        if ($hideZero && abs($n) < 0.0000001) {
            return '';
        }
        return number_format($n, 2, ',', ' ');
    };

    // 🔥 Dates formatées pour l'en-tête
    $dateDebutFmt = $date_debut ? Carbon::parse($date_debut)->format('d/m/Y') : '';
    $dateFinFmt = $date_fin ? Carbon::parse($date_fin)->format('d/m/Y') : '';
@endphp

<div class="page-border">

    <!-- EN-TÊTE -->
    <div class="header">
        <h2>ECAR Masina Maria Mpanampy</h2>
        <p>Tatitry ny Volan'ny Fikambanana / Vaomiera / Faritra</p>
        <p><strong>Détail par compte</strong></p>
    </div>

    <!-- INFOS -->
    <div class="info">
        <strong>Compte :</strong> {{ $compte }} - {{ $libelleCompte }} <br>
        <strong>Période :</strong> {{ $dateDebutFmt }} au {{ $dateFinFmt }}
    </div>

    <!-- TABLEAU -->
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>N°</th>
                <th>Libellé</th>
                <th>Montant</th>
                <th>Recette</th>
                <th>Dépense</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $r)
                @php
                    $dateVal = data_get($r, 'J_DETAIL_DATE') ?? data_get($r, 'j_detail_date');
                    $numero = data_get($r, 'J_DETAIL_NUMERO') ?? data_get($r, 'j_detail_numero');
                    $libelle = data_get($r, 'J_DETAIL_LIBELLE') ?? data_get($r, 'j_detail_libelle');
                    $montant = data_get($r, 'J_DETAIL_MONTANT') ?? data_get($r, 'j_detail_montant');
                    $recette = data_get($r, 'RECETTE_G') ?? data_get($r, 'recette_g');
                    $depense = data_get($r, 'DEPENSE_G') ?? data_get($r, 'depense_g');

                    // 🔥 Formater la date
                    $dateFmt = $dateVal ? Carbon::parse($dateVal)->format('d/m/Y') : '';
                @endphp

                <tr>
                    <td>{{ $dateFmt }}</td>
                    <td>{{ $numero }}</td>
                    <td>{{ $libelle }}</td>
                    <td class="right">{{ $fmt($montant) }}</td>
                    <td class="right">{{ $fmt($recette) }}</td>
                    <td class="right">{{ $fmt($depense) }}</td>
                </tr>
            @endforeach

            <!-- TOTAL -->
            <tr class="total">
                <td colspan="4" class="right">TOTAL</td>
                <td class="right">{{ $fmt($totalRecette, false) }}</td>
                <td class="right">{{ $fmt($totalDepense, false) }}</td>
            </tr>

            <!-- SOLDE -->
            <tr class="total">
                <td colspan="4" class="right">SOLDE</td>
                <td colspan="2" class="right">{{ $fmt($solde, false) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- SIGNATURE -->
    <table class="signature">
        <tr>
            <td>Le Comptable</td>
            <td>Le Curé</td>
        </tr>
        <tr>
            <td>(Signature)</td>
            <td>(Signature)</td>
        </tr>
    </table>

</div>

<!-- PAGINATION -->
<script type="text/php">
if (isset($pdf)) {
    $pdf->page_text(500, 820, "Page {PAGE_NUM} / {PAGE_COUNT}", null, 8);
}
</script>

</body>
</html>