<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f2f2f2; text-align: left; }
    </style>
</head>
<body>
<h3>ECAR - Liste des fidèles</h3>
<table>
    <thead>
    <tr>
        <th>Matricule</th><th>Nom</th><th>Prénom</th><th>Baptême</th>
        <th>Sexe</th><th>Statut</th><th>Faritra</th><th>APV</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $r)
        <tr>
            <td>{{ $r->MATRICULE }}</td><td>{{ $r->NOM }}</td><td>{{ $r->PRENOM }}</td><td>{{ $r->NOM_BAPTEME }}</td>
            <td>{{ $r->SEXE }}</td><td>{{ $r->STATUT }}</td><td>{{ $r->IDFARITRA }}</td><td>{{ $r->IDAPV }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>