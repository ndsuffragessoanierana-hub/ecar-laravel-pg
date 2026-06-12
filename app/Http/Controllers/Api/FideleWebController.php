<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\FidelesExport;
use App\Models\Apv;
use App\Models\Faritra;
use App\Models\Fidele;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class FideleWebController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q', ''));
        $faritra = trim((string) $request->get('faritra', ''));
        $apv = trim((string) $request->get('apv', ''));

        // Liste déroulante Faritra
        $faritras = DB::table('FARITRA')
            ->selectRaw('TRIM(IDFARITRA) as idfaritra, LIBELLE_FARITRA as libelle_faritra')
            ->orderBy('LIBELLE_FARITRA')
            ->get();

        // Liste déroulante APV (filtrée si faritra choisi)
        $apvsQuery = DB::table('APV')
            ->selectRaw('TRIM(IDAPV) as idapv, LIBELLE_APV as libelle_apv, TRIM(IDFARITRA) as idfaritra');

        if ($faritra !== '') {
            $apvsQuery->whereRaw('TRIM(IDFARITRA) = ?', [$faritra]);
        }

        $apvs = $apvsQuery
            ->orderBy('LIBELLE_APV')
            ->get();

        // Liste fidèles
        $fideles = DB::table('FIDELE')
            ->selectRaw("
                TRIM(MATRICULE) as matricule,
                NOM as nom,
                PRENOM as prenom,
                NOM_BAPTEME as nom_bapteme,
                SEXE as sexe,
                STATUT as statut,
                TRIM(IDFARITRA) as idfaritra,
                TRIM(IDAPV) as idapv
            ")
            ->when($search !== '', function ($q) use ($search) {
                $s = mb_strtoupper($search);
                $q->where(function ($qq) use ($s) {
                    $qq->whereRaw('UPPER(TRIM(MATRICULE)) LIKE ?', ["%{$s}%"])
                        ->orWhereRaw('UPPER(NOM) LIKE ?', ["%{$s}%"])
                        ->orWhereRaw('UPPER(PRENOM) LIKE ?', ["%{$s}%"]);
                });
            })
            ->when($faritra !== '', fn($q) => $q->whereRaw('TRIM(IDFARITRA) = ?', [$faritra]))
            ->when($apv !== '', fn($q) => $q->whereRaw('TRIM(IDAPV) = ?', [$apv]))
            ->orderBy('nom')
            ->orderBy('prenom')
            ->paginate(15)
            ->withQueryString();

        return view('fideles.index', compact(
            'fideles',
            'faritras',
            'apvs',
            'search',
            'faritra',
            'apv'
        ));
    }

    // Endpoint AJAX pour recharger APV quand faritra change
    public function apvByFaritra(string $idfaritra)
    {
        $rows = DB::table('APV')
            ->selectRaw('TRIM(IDAPV) as idapv, LIBELLE_APV as libelle_apv, TRIM(IDFARITRA) as idfaritra')
            ->whereRaw('TRIM(IDFARITRA) = ?', [trim($idfaritra)])
            ->orderBy('LIBELLE_APV')
            ->get();

        return response()->json($rows);
    }

    // update fidele
    public function update(Request $request, string $matricule)
{
    $data = $request->validate([
        'nom' => 'nullable|string|max:255',
        'prenom' => 'nullable|string|max:255',
        'nom_bapteme' => 'nullable|string|max:255',
        'statut' => 'nullable|string|max:100',
        'idfaritra' => 'nullable|string|max:20',
        'idapv' => 'nullable|string|max:20',
    ], [
        'nom.max' => 'Le nom est trop long.',
        'prenom.max' => 'Le prénom est trop long.',
    ]);

    DB::table('FIDELE')
        ->whereRaw('TRIM(MATRICULE) = ?', [trim($matricule)])
        ->update([
            'NOM' => $data['nom'] ?? null,
            'PRENOM' => $data['prenom'] ?? null,
            'NOM_BAPTEME' => $data['nom_bapteme'] ?? null,
            'STATUT' => $data['statut'] ?? null,
            'IDFARITRA' => $data['idfaritra'] ?? null,
            'IDAPV' => $data['idapv'] ?? null,
        ]);

    return redirect()->route('fideles.index', $request->query())
        ->with('success', 'Fidèle mis à jour avec succès.');
    }
}