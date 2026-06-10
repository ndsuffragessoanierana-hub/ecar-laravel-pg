<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Livre Journal</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 100px 20px 60px 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            text-align: center;
            line-height: 1.5;
        }

        footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            text-align: right;
            font-size: 10px;
        }

        .page-number:after {
            content: "Page " counter(page);
        }

        .title {
            font-size: 16px;
            font-weight: bold;
        }

        .box {
            border: 1px solid black;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 4px;
        }

        th {
            background-color: #f0f0f0;
            text-align: center;
        }

        td.right {
            text-align: right;
        }

        .totaux {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .signature {
            margin-top: 30px;
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

<header>
    <table style="width:100%; border:none;">
        <tr>

            <!-- Logo -->
            <td style="width:20%; text-align:left; border:none;">
                <img src="{{ public_path('images/ND.png') }}"
                     alt="Logo"
                     style="height:60px;">
            </td>

            <!-- Titre -->
            <td style="width:60%; text-align:center; border:none;">
                <div class="title">ECAR MASINA MARIA MPANAMPY SOANIERANA - LIVRE JOURNAL</div>
                <div class="title">Période : {{ $periode }}</div>
            </td>

            <!-- Vide -->
            <td style="width:20%; border:none;"></td>

        </tr>
    </table>
</header>

<footer>
    <div class="page-number"></div>
</footer>

<!-- ===================================== -->
<!-- RECAP -->
<!-- ===================================== -->

<h3 style="text-align:center;">
    RÉCAPITULATIF DES RECETTES ET DÉPENSES
</h3>

<table>
    <thead>
        <tr>
            <th>Code</th>
            <th>Rubrique</th>
            <th>Recette</th>
            <th>Dépense</th>
        </tr>
    </thead>

    <tbody>

        @php
            $totalRecapRecette = 0;
            $totalRecapDepense = 0;
        @endphp

        @foreach($recap as $r)

            <tr>

                <td>{{ $r->rubrique_id }}</td>

                <td>{{ $r->rubrique_libelle }}</td>

                <td class="right">
                    {{ $r->recette ? number_format($r->recette,2,',',' ') : '' }}
                </td>

                <td class="right">
                    {{ $r->depense ? number_format($r->depense,2,',',' ') : '' }}
                </td>

            </tr>

            @php
                $totalRecapRecette += $r->recette ?? 0;
                $totalRecapDepense += $r->depense ?? 0;
            @endphp

        @endforeach

        <tr class="totaux">

            <td colspan="2">TOTAL</td>

            <td class="right">
                {{ $totalRecapRecette != 0 ? number_format($totalRecapRecette,2,',',' ') : '' }}
            </td>

            <td class="right">
                {{ $totalRecapDepense != 0 ? number_format($totalRecapDepense,2,',',' ') : '' }}
            </td>

        </tr>

    </tbody>
</table>

<br><br>

<!-- ===================================== -->
<!-- SOLDE -->
<!-- ===================================== -->

<table style="width:100%; border:none;">

    <tbody>

        <tr>

            <!-- GAUCHE -->
            <td style="width:50%; vertical-align:top;">

                <table>
                    <tbody>

                        <tr>
                            <th colspan="2">Solde précédent</th>
                        </tr>

                        <tr>
                            <td>BNI</td>

                            <td class="right">
                                {{ $soldePrecedent->journal_solde_bni != 0 ? number_format($soldePrecedent->journal_solde_bni,2,',',' ') : '' }}
                            </td>
                        </tr>

                        <tr>
                            <td>BRED</td>

                            <td class="right">
                                {{ $soldePrecedent->journal_solde_bfv != 0 ? number_format($soldePrecedent->journal_solde_bfv,2,',',' ') : '' }}
                            </td>
                        </tr>

                        <tr>
                            <td>Caisse</td>

                            <td class="right">
                                {{ $soldePrecedent->journal_solde_caisse != 0 ? number_format($soldePrecedent->journal_solde_caisse,2,',',' ') : '' }}
                            </td>
                        </tr>

                        <tr class="totaux">
                            <td>Total</td>

                            <td class="right">
                                {{ $soldePrecedent->total != 0 ? number_format($soldePrecedent->total,2,',',' ') : '' }}
                            </td>
                        </tr>

                    </tbody>
                </table>

            </td>

            <!-- DROITE -->
            <td style="width:50%; vertical-align:top;">

                <table>
                    <tbody>

                        <tr>
                            <th colspan="2">
                                Solde à la fin du mois {{ $periode }}
                            </th>
                        </tr>

                        <tr>
                            <td>BNI</td>

                            <td class="right">
                                {{ $soldeCourant->journal_solde_bni != 0 ? number_format($soldeCourant->journal_solde_bni,2,',',' ') : '' }}
                            </td>
                        </tr>

                        <tr>
                            <td>BFV</td>

                            <td class="right">
                                {{ $soldeCourant->journal_solde_bfv != 0 ? number_format($soldeCourant->journal_solde_bfv,2,',',' ') : '' }}
                            </td>
                        </tr>

                        <tr>
                            <td>Caisse</td>

                            <td class="right">
                                {{ $soldeCourant->journal_solde_caisse != 0 ? number_format($soldeCourant->journal_solde_caisse,2,',',' ') : '' }}
                            </td>
                        </tr>

                        <tr class="totaux">

                            <td>Total</td>

                            <td class="right">
                                {{ $soldeCourant->total != 0 ? number_format($soldeCourant->total,2,',',' ') : '' }}
                            </td>

                        </tr>

                    </tbody>
                </table>

            </td>

        </tr>

    </tbody>

</table>

<br>

<!-- ===================================== -->
<!-- SIGNATURE -->
<!-- ===================================== -->

<table class="signature">
    <tr>
        <td>Ampandalovina teo amin'ny Pretra </td>
        <td>Le Trésorier</td>
    </tr>
</table>

<!-- ===================================== -->
<!-- SAUT DE PAGE -->
<!-- ===================================== -->

<div style="page-break-before: always;"></div>

<!-- ===================================== -->
<!-- DETAILS -->
<!-- ===================================== -->

<main class="box">

    <table>

        <thead>

            <tr>
                <th>N°</th>
                <th>Date</th>
                <th>Libellé</th>
                <th>Recette</th>
                <th>Dépense</th>
                <th>Recette ESPECES</th>
                <th>Dépense ESPECES</th>
                <th>Recette BRED</th>
                <th>Dépense BRED</th>
                <th>Recette BNI</th>
                <th>Dépense BNI</th>
            </tr>

        </thead>

        <tbody>

            @php
                $totals = [
                    'recette_g'=>0,
                    'depense_g'=>0,
                    'recette_num'=>0,
                    'depense_num'=>0,
                    'recette_bfv'=>0,
                    'depense_bfv'=>0,
                    'recette_bni'=>0,
                    'depense_bni'=>0
                ];
            @endphp

            @foreach($details as $d)

                <tr>

                    <td>{{ $d->J_DETAIL_NUMERO ?? $d->j_detail_numero ?? '' }}</td>

                    <td>
                        @if($d->J_DETAIL_DATE ?? $d->j_detail_date)
                            {{ \Carbon\Carbon::parse($d->J_DETAIL_DATE ?? $d->j_detail_date)->format('d/m/Y') }}
                        @endif
                    </td>

                    <td>{{ $d->J_DETAIL_LIBELLE ?? $d->j_detail_libelle ?? '' }}</td>

                    <td class="right">
                        {{ $d->recette_g ? number_format($d->recette_g,2,',',' ') : '' }}
                    </td>

                    <td class="right">
                        {{ $d->depense_g ? number_format($d->depense_g,2,',',' ') : '' }}
                    </td>

                    <td class="right">
                        {{ $d->recette_num ? number_format($d->recette_num,2,',',' ') : '' }}
                    </td>

                    <td class="right">
                        {{ $d->depense_num ? number_format($d->depense_num,2,',',' ') : '' }}
                    </td>

                    <td class="right">
                        {{ $d->recette_bfv ? number_format($d->recette_bfv,2,',',' ') : '' }}
                    </td>

                    <td class="right">
                        {{ $d->depense_bfv ? number_format($d->depense_bfv,2,',',' ') : '' }}
                    </td>

                    <td class="right">
                        {{ $d->recette_bni ? number_format($d->recette_bni,2,',',' ') : '' }}
                    </td>

                    <td class="right">
                        {{ $d->depense_bni ? number_format($d->depense_bni,2,',',' ') : '' }}
                    </td>

                </tr>

                @php
                    foreach($totals as $key => $val){
                        $totals[$key] += isset($d->$key) ? $d->$key : 0;
                    }
                @endphp

            @endforeach

            <tr class="totaux">

                <td colspan="3">TOTAL</td>

                @foreach($totals as $val)

                    <td class="right">
                        {{ $val != 0 ? number_format($val,2,',',' ') : '' }}
                    </td>

                @endforeach

            </tr>

        </tbody>

    </table>

</main>

</body>
</html>