<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h3 { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background: #f2f2f2; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

<h3>LISTE DES BIENS AVEC QR CODE</h3>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Dénomination</th>
            <th>Référence</th>            
            <th>Place</th>
            <th>N° Inventaire</th>
            <th>QR Code</th>
        </tr>
    </thead>

    <tbody>
        @foreach($biens as $b)
        <tr>
            <td>{{ $b->IDFITAOVANA ?? $b->idfitaovana ?? '' }}</td>
            <td>{{ $b->DENOMINATION ?? $b->denomination ?? '' }}</td>
            <td>{{ $b->REFERENCE ?? $b->reference ?? '' }}</td> 
            <td>{{ $b->libelle_place ?? $b->LIBELLE_PLACE ?? '' }}</td> 
            <td>{{ $b->no_inventaire ?? $b->NO_INVENTAIRE ?? '' }}</td>
            <td class="text-center">
                @if(!empty($b->qr_code))
                    <img src="data:image/png;base64,{{ base64_encode($b->qr_code) }}" width="30">
                @else
                    -
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>