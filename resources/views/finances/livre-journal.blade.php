@extends('layouts.app')

@section('title', 'Livre Journal - ECAR')
@section('page_title', 'Finances / Livre Journal')

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

    $selectedJournal = collect($journaux)->firstWhere('journal_id', request('journal_id', $journaux[0]->journal_id ?? null));
@endphp

<div class="space-y-4">

    <!-- 🔹 Bloc filtre -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
        <h2 class="text-xl font-semibold mb-4">Livre Journal</h2>

        <div class="flex flex-wrap gap-3 items-end">

            <form method="GET" action="{{ route('finances.livre_journal') }}" class="flex flex-wrap gap-3 flex-1">

                <select name="journal_id"
                    class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 px-3 py-2 w-full md:w-auto">

                    @foreach($journaux as $j)
                        <option value="{{ $j->journal_id }}"
                            @selected(request('journal_id', $journaux[0]->journal_id ?? null) == $j->journal_id)>
                            {{ strtoupper($j->journal_mois) }} {{ $j->journal_annee }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                    class="rounded-xl bg-ecar-600 hover:bg-ecar-700 text-white px-6 py-2 w-36">
                    Afficher
                </button>
            </form>

            <a href="{{ route('finances.livre_journal_pdf', ['journal_id' => request('journal_id')]) }}"
               target="_blank"
               class="inline-flex items-center justify-center px-6 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-slate-100 dark:hover:bg-white dark:text-slate-900 text-sm w-36">
                Imprimer PDF
            </a>
        </div>
    </div>

    <!-- 🔹 Période -->
    @if($selectedJournal)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 text-center font-semibold">
            PÉRIODE : {{ strtoupper($selectedJournal->journal_mois) }} {{ $selectedJournal->journal_annee }}
        </div>
    @endif

    <!-- 🔹 Tableau -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">

        @if(isset($details) && count($details) > 0)

            @php
                $totalRecette = collect($details)->sum(fn($d) => (float) ($d->recette_g ?? 0));
                $totalDepense = collect($details)->sum(fn($d) => (float) ($d->depense_g ?? 0));
            @endphp

            <!-- Totaux -->
            <div class="flex flex-wrap gap-3 mb-3 text-sm">
                <div class="px-3 py-2 rounded bg-emerald-50 text-emerald-700">
                    Total Recettes: {{ $fmt($totalRecette) ?: '-' }}
                </div>
                <div class="px-3 py-2 rounded bg-rose-50 text-rose-700">
                    Total Dépenses: {{ $fmt($totalDepense) ?: '-' }}
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-red-700 text-white">
                        <tr>
                            <th class="px-3 py-2 text-left">Date</th>
                            <th class="px-3 py-2 text-left">N°</th>
                            <th class="px-3 py-2 text-left">Libellé</th>
                            <th class="px-3 py-2 text-left">Mode paie</th>
                            <th class="px-3 py-2 text-right">Recette</th>
                            <th class="px-3 py-2 text-right">Dépense</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($details as $d)
                            @php
                                $date = $v($d, 'j_detail_date', 'J_DETAIL_DATE');
                                $numero = $v($d, 'j_detail_numero', 'J_DETAIL_NUMERO');
                                $libelle = $v($d, 'j_detail_libelle', 'J_DETAIL_LIBELLE');
                                $mode = $v($d, 'j_detail_mode_paie', 'J_DETAIL_MODE_PAIE');
                                $recette = $v($d, 'recette_g', 'RECETTE_G');
                                $depense = $v($d, 'depense_g', 'DEPENSE_G');
                            @endphp

                            <tr>
                                <td class="px-3 py-2">
                                    @if($date)
                                        {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td class="px-3 py-2">{{ $numero }}</td>
                                <td class="px-3 py-2">{{ $libelle }}</td>
                                <td class="px-3 py-2">{{ $mode }}</td>
                                <td class="px-3 py-2 text-right">{{ $fmt($recette) }}</td>
                                <td class="px-3 py-2 text-right">{{ $fmt($depense) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @else
            @if(request()->has('journal_id'))
                <div class="text-center text-slate-500 py-4">
                    Aucun détail pour ce journal.
                </div>
            @endif
        @endif

    </div>

</div>
@endsection