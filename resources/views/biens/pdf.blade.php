<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>
        body {
            font-size: 12px;
            font-family: DejaVu Sans, sans-serif;
        }

        h3 {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        /* Footer pagination */
        footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            text-align: right;
            font-size: 10px;
        }

        .page-number:after {
            content: "Page " counter(page)  ;
        }
    </style>
</head>
<body>

<header>
    <h3>LISTE DES BIENS</h3>
</header>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Dénomination</th>
            <th>Référence</th>
            <th>Date</th>
            <th>Valeur</th>
            <th>Qté</th>
            <th>Emplacement</th>
            <th>Lieu</th>
        </tr>
    </thead>

    <tbody>
        @foreach($biens as $b)
            @php
                $valeur = (float) ($b->valeur_acquisition ?? 0);
                $qte = (float) ($b->qte_achetee ?? 0);
            @endphp

            <tr>
                <td>{{ $b->idfitaovana }}</td>
                <td>{{ $b->denomination }}</td>
                <td>{{ $b->reference }}</td>

                <!-- ✅ Date formatée -->
                <td>
                    @if($b->date_acquisition)
                        {{ \Carbon\Carbon::parse($b->date_acquisition)->format('d/m/Y') }}
                    @endif
                </td>

                <!-- ✅ Valeur alignée + zéro caché -->
                <td class="text-right">
                    @if(abs($valeur) > 0.00001)
                        {{ number_format($valeur, 2, ',', ' ') }}
                    @endif
                </td>

                <!-- ✅ Quantité alignée -->
                <td class="text-right">
                    @if(abs($qte) > 0.00001)
                        {{ number_format($qte, 0, ',', ' ') }}
                    @endif
                </td>

                <td>{{ $b->libelle_place }}</td>
                <td>{{ $b->toerana }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- ✅ Footer pagination -->
<footer>
    <div class="page-number"></div>
</footer>

</body>
</html>