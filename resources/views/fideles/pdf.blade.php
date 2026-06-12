<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Fidèles</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 5px; }
        th { background: #eee; }
    </style>
</head>
<body>

<h3>Liste des Fidèles</h3>

<table>
    <thead>
        <tr>
            <th>Matricule</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $r)
            <tr>
                <td>{{ $r->matricule }}</td>
                <td>{{ $r->nom }}</td>
                <td>{{ $r->prenom }}</td>
                <td>{{ $r->statut }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>