@extends('layouts.app')

@section('title', 'Fidèles - ECAR')
@section('page_title', 'Fidèles')

@section('content')
@if(session('success'))
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 px-4 py-3 mb-4">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-700 px-4 py-3 mb-4">
        <ul class="list-disc ml-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-6">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Liste des fidèles</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Consultation, recherche et export</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('fideles.export.excel', request()->query()) }}"
                   class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm">
                    Export Excel
                </a>
                <a href="{{ route('fideles.export.pdf', request()->query()) }}"
                   class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-sm">
                    Export PDF
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('fideles.index') }}" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="Matricule, nom, prénom"
                class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"
            >

            <select name="faritra" id="faritra"
                    class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                <option value="">-- Faritra --</option>
                @foreach(($faritras ?? []) as $fr)
                    @php
                        $idFaritra = data_get($fr, 'idfaritra') ?? data_get($fr, 'IDFARITRA');
                        $libFaritra = data_get($fr, 'libelle_faritra') ?? data_get($fr, 'LIBELLE_FARITRA');
                    @endphp
                    <option value="{{ $idFaritra }}" @selected(($faritra ?? '') == $idFaritra)>{{ $libFaritra }}</option>
                @endforeach
            </select>

            <select name="apv" id="apv"
                    class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                <option value="">-- APV --</option>
                @foreach(($apvs ?? []) as $a)
                    @php
                        $idApv = data_get($a, 'idapv') ?? data_get($a, 'IDAPV');
                        $libApv = data_get($a, 'libelle_apv') ?? data_get($a, 'LIBELLE_APV');
                    @endphp
                    <option value="{{ $idApv }}" @selected(($apv ?? '') == $idApv)>{{ $libApv }}</option>
                @endforeach
            </select>

            <button type="submit" class="rounded-xl bg-ecar-600 hover:bg-ecar-700 text-white px-4 py-2">
                Filtrer
            </button>
        </form>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-500 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">Action</th>
                        <th class="px-4 py-3 text-left">Matricule</th>
                        <th class="px-4 py-3 text-left">Nom</th>
                        <th class="px-4 py-3 text-left">Prénom</th>
                        <th class="px-4 py-3 text-left">Nom baptême</th>
                        <th class="px-4 py-3 text-left">Statut</th>
                        <th class="px-4 py-3 text-left">Faritra</th>
                        <th class="px-4 py-3 text-left">APV</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($fideles as $f)
                        @php
                            $matricule = data_get($f, 'matricule') ?? data_get($f, 'MATRICULE');
                            $nom = data_get($f, 'nom') ?? data_get($f, 'NOM');
                            $prenom = data_get($f, 'prenom') ?? data_get($f, 'PRENOM');
                            $nomBapteme = data_get($f, 'nom_bapteme') ?? data_get($f, 'NOM_BAPTEME');
                            $statut = data_get($f, 'statut') ?? data_get($f, 'STATUT');
                            $idfaritra = data_get($f, 'idfaritra') ?? data_get($f, 'IDFARITRA');
                            $idapv = data_get($f, 'idapv') ?? data_get($f, 'IDAPV');
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <td class="px-4 py-3">
                                <button type="button"
                                    class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700"
                                    title="Modifier"
                                    data-matricule="{{ $matricule }}"
                                    data-nom="{{ $nom }}"
                                    data-prenom="{{ $prenom }}"
                                    data-nom-bapteme="{{ $nomBapteme }}"
                                    data-statut="{{ $statut }}"
                                    data-idfaritra="{{ $idfaritra }}"
                                    data-idapv="{{ $idapv }}"
                                    onclick="openEditModalFromButton(this)">
                                    ✏️
                                </button>
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $matricule }}</td>
                            <td class="px-4 py-3">{{ $nom }}</td>
                            <td class="px-4 py-3">{{ $prenom }}</td>
                            <td class="px-4 py-3">{{ $nomBapteme }}</td>
                            <td class="px-4 py-3">{{ $statut }}</td>
                            <td class="px-4 py-3">{{ $idfaritra }}</td>
                            <td class="px-4 py-3">{{ $idapv }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-slate-500">Aucune donnée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800">
            {{ $fideles->links() }}
        </div>
    </div>
</div>

<!-- Modal Edition -->
<div id="editModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-lg font-semibold">Modifier un fidèle</h3>
            <button type="button" onclick="closeEditModal()">✕</button>
        </div>

        <form id="editForm" method="POST" class="p-5 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="text-sm text-slate-500">Matricule</label>
                    <input id="e_matricule" type="text" disabled class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                </div>
                <div>
                    <label class="text-sm text-slate-500">Statut</label>
                    <input name="statut" id="e_statut" type="text" class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                </div>

                <div>
                    <label class="text-sm text-slate-500">Nom</label>
                    <input name="nom" id="e_nom" type="text" class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                </div>
                <div>
                    <label class="text-sm text-slate-500">Prénom</label>
                    <input name="prenom" id="e_prenom" type="text" class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                </div>

                <div>
                    <label class="text-sm text-slate-500">Nom baptême</label>
                    <input name="nom_bapteme" id="e_nom_bapteme" type="text" class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                </div>

                <div>
                    <label class="text-sm text-slate-500">Faritra</label>
                    <select name="idfaritra" id="e_idfaritra" class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                        <option value="">-- Faritra --</option>
                        @foreach(($faritras ?? []) as $fr)
                            @php
                                $idFaritra = data_get($fr, 'idfaritra') ?? data_get($fr, 'IDFARITRA');
                                $libFaritra = data_get($fr, 'libelle_faritra') ?? data_get($fr, 'LIBELLE_FARITRA');
                            @endphp
                            <option value="{{ $idFaritra }}">{{ $libFaritra }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm text-slate-500">APV</label>
                    <select name="idapv" id="e_idapv" class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                        <option value="">-- APV --</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-ecar-600 hover:bg-ecar-700 text-white">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModalFromButton(btn) {
    openEditModal({
        matricule: btn.dataset.matricule || '',
        nom: btn.dataset.nom || '',
        prenom: btn.dataset.prenom || '',
        nom_bapteme: btn.dataset.nomBapteme || '',
        statut: btn.dataset.statut || '',
        idfaritra: btn.dataset.idfaritra || '',
        idapv: btn.dataset.idapv || ''
    });
}

async function loadApvOptions(faritraId, selectedApv = '') {
    const apvSelect = document.getElementById('e_idapv');
    apvSelect.innerHTML = '<option value="">-- APV --</option>';

    if (!faritraId) return;

    try {
        const res = await fetch(`/api/apv-by-faritra/${encodeURIComponent(faritraId)}`);
        const rows = await res.json();

        rows.forEach(row => {
            const opt = document.createElement('option');
            opt.value = row.idapv ?? row.IDAPV ?? '';
            opt.textContent = row.libelle_apv ?? row.LIBELLE_APV ?? '';
            if (selectedApv && opt.value === selectedApv) opt.selected = true;
            apvSelect.appendChild(opt);
        });
    } catch (e) {
        console.error('Erreur chargement APV modal:', e);
    }
}

function openEditModal(row) {
    const form = document.getElementById('editForm');
    const modal = document.getElementById('editModal');

    document.getElementById('e_matricule').value = row.matricule || '';
    document.getElementById('e_nom').value = row.nom || '';
    document.getElementById('e_prenom').value = row.prenom || '';
    document.getElementById('e_nom_bapteme').value = row.nom_bapteme || '';
    document.getElementById('e_statut').value = row.statut || '';
    document.getElementById('e_idfaritra').value = row.idfaritra || '';

    const qs = new URLSearchParams(window.location.search).toString();
    form.action = `/fideles/${encodeURIComponent(row.matricule)}${qs ? ('?' + qs) : ''}`;

    loadApvOptions(row.idfaritra || '', row.idapv || '');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.addEventListener('DOMContentLoaded', function () {
    const faritra = document.getElementById('faritra');
    const apv = document.getElementById('apv');
    const eFaritra = document.getElementById('e_idfaritra');

    if (faritra && apv) {
        faritra.addEventListener('change', async function () {
            const id = (this.value || '').trim();
            apv.innerHTML = '<option value="">-- APV --</option>';
            if (!id) return;

            try {
                const res = await fetch(`/api/apv-by-faritra/${encodeURIComponent(id)}`);
                const rows = await res.json();

                rows.forEach(row => {
                    const opt = document.createElement('option');
                    opt.value = row.idapv ?? row.IDAPV ?? '';
                    opt.textContent = row.libelle_apv ?? row.LIBELLE_APV ?? '';
                    apv.appendChild(opt);
                });
            } catch (e) {
                console.error('Erreur chargement APV:', e);
            }
        });
    }

    if (eFaritra) {
        eFaritra.addEventListener('change', function () {
            loadApvOptions(this.value, '');
        });
    }
});
</script>
@endsection