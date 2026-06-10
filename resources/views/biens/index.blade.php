@extends('layouts.app')

@section('title', 'Liste des biens - ECAR')
@section('page_title', 'Biens / Liste des biens')

@section('content')

@php
    $fmt = fn($v) => number_format((float)$v, 2, ',', ' ');
@endphp

<div class="space-y-4">

    <!-- 🔹 Header -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 p-2">
        <h2 class="text-xl font-semibold mb-4">Liste des biens</h2>

        <div class="flex justify-end">
            <a href="{{ route('biens.pdf') }}"
               class="inline-flex items-center justify-center px-6 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm w-40">
                Imprimer PDF
            </a>
        </div>
    </div>

    <!-- 🔹 Tableau -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 p-5">

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-blue-900 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left">ID</th>
                        <th class="px-3 py-2 text-left">Dénomination</th>
                        <th class="px-3 py-2 text-left">Référence</th>
                        <th class="px-3 py-2 text-left">Date acquisition</th>
                        <th class="px-3 py-2 text-center">Valeur</th>
                        <th class="px-3 py-2 text-right">Quantité</th>
                        <th class="px-3 py-2 text-left">Emplacement</th>
                        <th class="px-3 py-2 text-left">Lieu</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($biens as $b)
                        <tr>
                            <td class="px-3 py-2">{{ $b->idfitaovana }}</td>
                            <td class="px-3 py-2">{{ $b->denomination }}</td>
                            <td class="px-3 py-2">{{ $b->reference }}</td>

                            <td class="px-3 py-2">
                                @if($b->date_acquisition)
                                    {{ \Carbon\Carbon::parse($b->date_acquisition)->format('d/m/Y') }}
                                @endif
                            </td>

                            <td class="px-3 py-2 text-right">
                                {{ ($b->valeur_acquisition ?? 0) != 0 ? $fmt($b->valeur_acquisition) : '' }}
                            </td>

                            <td class="px-3 py-2 text-right">
                                {{ $b->qte_achetee }}
                            </td>

                            <td class="px-3 py-2">{{ $b->libelle_place }}</td>
                            <td class="px-3 py-2">{{ $b->toerana }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-slate-500">
                                Aucun bien trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection