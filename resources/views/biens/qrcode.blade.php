@extends('layouts.app')

@section('title', 'Biens avec QR Code')
@section('page_title', 'Biens / QR Code')

@section('content')

<div class="space-y-4">

    <!-- Header -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border p-4">
        <h2 class="text-xl font-semibold mb-4">Liste des biens avec QR Code</h2>

        <div class="flex justify-end">
            <a href="{{ route('biens.qrcode.pdf') }}"
               class="px-4 py-2 bg-slate-900 text-white rounded-xl">
                Imprimer PDF
            </a>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border p-5">

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">

                <thead class="bg-blue-900 text-white">
                    <tr>
                        <th class="px-3 py-2 text-left">ID</th>
                        <th class="px-3 py-2 text-left">Dénomination</th>
                        <th class="px-3 py-2 text-left">Référence</th>
                        <th class="px-3 py-2 text-left">Remarque</th>
                        <th class="px-3 py-2 text-left">Localisation</th>
                        <th class="px-3 py-2 text-left">N° Inventaire</th>
                        <th class="px-3 py-2 text-center">QR Code</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                    @forelse($biens as $b)
                        <tr>
                            <td class="px-3 py-2">{{ $b->IDFITAOVANA ?? $b->idfitaovana }}</td>
                            <td class="px-3 py-2">{{ $b->DENOMINATION ?? $b->denomination }}</td>
                            <td class="px-3 py-2">{{ $b->REFERENCE ?? $b->reference }}</td>
                            <td class="px-3 py-2">{{ $b->REMARQUE ?? $b->remarque }}</td>
                            <td class="px-3 py-2">{{ $b->LOCALISATION ?? $b->localisation }}</td>
                            <td class="px-3 py-2">{{ $b->NO_INVENTAIRE ?? $b->no_inventaire }}</td>
                            <!-- 🔥 QR CODE -->
                            <td class="px-3 py-2 text-center">
                                {!! QrCode::size(70)->generate($b->QR_TEXT ?? $b->IDFITAOVANA ?? $b->idfitaovana) !!}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-slate-500">
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