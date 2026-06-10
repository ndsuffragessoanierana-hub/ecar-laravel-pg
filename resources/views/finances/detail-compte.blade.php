<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Détail par compte</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background: #eee; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

<h2>Détail par compte</h2>

<p>
    Compte: {{ $compte }} <br>
    Période: {{ $date_debut }} - {{ $date_fin }}
</p>

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
            <tr>
                <td>{{ $r->date ?? '' }}</td>
                <td>{{ $r->numero ?? '' }}</td>
                <td>{{ $r->libelle ?? '' }}</td>
                <td class="text-right">{{ $r->montant ?? '' }}</td>
                <td class="text-right">{{ $r->recette ?? '' }}</td>
                <td class="text-right">{{ $r->depense ?? '' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p>
    Total Recettes: {{ $totalRecette }} <br>
    Total Dépenses: {{ $totalDepense }} <br>
    Solde: {{ $solde }}
</p>

</body>
</html>