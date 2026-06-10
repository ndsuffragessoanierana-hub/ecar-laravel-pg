@extends('layouts.app')

@section('title', 'Détail par compte - ECAR')
@section('page_title', 'Finances / Détail par compte')

@section('content')
@php
    $fmt = function ($v, $hideZero = true) {
        $n = (float) ($v ?? 0);
        if ($hideZero && abs($n) < 0.0000001) {
            return '';
        }
        return number_format($n, 2, ',', ' ');
    };

    $v = function ($row, $lower, $upper = null) {
        return data_get($row, $lower) ?? ($upper ? data_get($row, $upper) : null);
    };

    $dateDebutFmt = $filters['date_debut'] ?? null;
    $dateFinFmt = $filters['date_fin'] ?? null;
@endphp

<div class="space-y-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
        <h2 class="text-xl font-semibold mb-4">Détail par compte</h2>

        <!-- Formulaire et boutons alignés -->
        <div class="flex flex-wrap gap-3 items-end">
            <form method="GET" action="{{ route('finances.detail_compte') }}" class="flex flex-wrap gap-3 flex-1">
                <select name="compte" class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 px-3 py-2 w-full md:w-auto">
                    <option value="">-- Choisir un compte --</option>
                    @foreach($comptes as $c)
                        @php
                            $noCompte = data_get($c, 'no_compte') ?? data_get($c, 'NO_COMPTE');
                            $libCompte = data_get($c, 'libelle_compte') ?? data_get($c, 'LIBELLE_COMPTE');
                        @endphp
                        <option value="{{ $noCompte }}" @selected(($filters['compte'] ?? '') == $noCompte)>
                            {{ $noCompte }} - {{ $libCompte }}
                        </option>
                    @endforeach
                </select>

                <input type="date" name="date_debut" value="{{ $filters['date_debut'] ?? '' }}"
                       class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 px-3 py-2">

                <input type="date" name="date_fin" value="{{ $filters['date_fin'] ?? '' }}"
                       class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 px-3 py-2">

                <button type="submit" class="rounded-xl bg-ecar-600 hover:bg-ecar-700 text-white px-6 py-2 w-36">
                    Afficher
                </button>
            </form>
<!--
            @if(!empty($filters['compte']) && !empty($filters['date_debut']) && !empty($filters['date_fin']))
                <a href="{{ route('finances.detail_compte_pdf', request()->query()) }}"
                   class="inline-flex items-center justify-center gap-2 px-6 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-slate-100 dark:hover:bg-white dark:text-slate-900 text-sm w-36">
                    Imprimer PDF
                </a>
            @endif
-->
            <a href="{{ route('finances.detail_compte_pdf', request()->query()) }}"
            class="inline-flex items-center justify-center gap-2 px-6 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm w-36">
                Imprimer PDF
            </a>


        </div>
    </div>

    <!-- Totaux et tableau -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
        <div class="flex flex-wrap gap-3 mb-3 text-sm">
            <div class="px-3 py-2 rounded bg-emerald-50 text-emerald-700">
                Total Recettes: {{ $fmt($totalRecette ?? 0, true) ?: '-' }}
            </div>
            <div class="px-3 py-2 rounded bg-rose-50 text-rose-700">
                Total Dépenses: {{ $fmt($totalDepense ?? 0, true) ?: '-' }}
            </div>
            <div class="px-3 py-2 rounded {{ ($solde ?? 0) >= 0 ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
                Solde: {{ $fmt($solde ?? 0, true) ?: '-' }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-red-800 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left">Date</th>
                        <th class="px-3 py-2 text-left">N°</th>
                        <th class="px-3 py-2 text-left">Libellé</th>
                        <th class="px-3 py-2 text-left">Mode paie</th>
                        <th class="px-3 py-2 text-right">Montant</th>
                        <th class="px-3 py-2 text-left">Rubrique</th>
                        <th class="px-3 py-2 text-right">Recette</th>
                        <th class="px-3 py-2 text-right">Dépense</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($rows as $r)
                        @php
                            $dateVal = $v($r, 'j_detail_date', 'J_DETAIL_DATE');
                            $numero = $v($r, 'j_detail_numero', 'J_DETAIL_NUMERO');
                            $libelle = $v($r, 'j_detail_libelle', 'J_DETAIL_LIBELLE');
                            $modePaie = $v($r, 'j_detail_mode_paie', 'J_DETAIL_MODE_PAIE');
                            $montant = $v($r, 'j_detail_montant', 'J_DETAIL_MONTANT');
                            $rubrique = $v($r, 'rub_rubrique_id', 'RUB_RUBRIQUE_ID');
                            $recette = $v($r, 'recette_g', 'RECETTE_G');
                            $depense = $v($r, 'depense_g', 'DEPENSE_G');
                        @endphp
                        <tr>
                            <td class="px-3 py-2">
                                @if($dateVal)
                                    {{ \Carbon\Carbon::parse($dateVal)->format('d/m/Y') }}
                                @endif
                            </td>
                            <td class="px-3 py-2">{{ $numero }}</td>
                            <td class="px-3 py-2">{{ $libelle }}</td>
                            <td class="px-3 py-2">{{ $modePaie }}</td>
                            <td class="px-3 py-2 text-right">{{ $fmt($montant, true) }}</td>
                            <td class="px-3 py-2">{{ $rubrique }}</td>
                            <td class="px-3 py-2 text-right">{{ $fmt($recette, true) }}</td>
                            <td class="px-3 py-2 text-right">{{ $fmt($depense, true) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-4 text-center text-slate-500">Aucune donnée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection