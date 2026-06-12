<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\fidelesExport;
use App\Models\Apv;
use App\Models\Faritra;
use App\Models\Fidele;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class fideleWebController extends Controller
{
	
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q', ''));
        $faritra = trim((string) $request->get('faritra', ''));
        $apv = trim((string) $request->get('apv', ''));

        // Liste déroulante faritra
        $faritras = DB::table('faritra')
            ->selectRaw('TRIM(idfaritra) as idfaritra, libelle_faritra as libelle_faritra')
            ->orderBy('libelle_faritra')
            ->get();

        // Liste déroulante apv (filtrée si faritra choisi)
        $apvsQuery = DB::table('apv')
            ->selectRaw('TRIM(idapv) as idapv, libelle_apv as libelle_apv, TRIM(idfaritra) as idfaritra');

        if ($faritra !== '') {
            $apvsQuery->whereRaw('TRIM(idfaritra) = ?', [$faritra]);
        }

        $apvs = $apvsQuery
            ->orderBy('libelle_apv')
            ->get();

        // Liste fidèles
        $fideles = DB::table('fidele')
            ->selectRaw("
                TRIM(matricule) as matricule,
                nom as nom,
                prenom as prenom,
                nom_bapteme as nom_bapteme,
                sexe as sexe,
                statut as statut,
                TRIM(idfaritra) as idfaritra,
                TRIM(idapv) as idapv
            ")
            ->when($search !== '', function ($q) use ($search) {
                $s = mb_strtoupper($search);
                $q->where(function ($qq) use ($s) {
                    $qq->whereRaw('UPPER(TRIM(matricule)) LIKE ?', ["%{$s}%"])
                        ->orWhereRaw('UPPER(nom) LIKE ?', ["%{$s}%"])
                        ->orWhereRaw('UPPER(prenom) LIKE ?', ["%{$s}%"]);
                });
            })
            ->when($faritra !== '', fn($q) => $q->whereRaw('TRIM(idfaritra) = ?', [$faritra]))
            ->when($apv !== '', fn($q) => $q->whereRaw('TRIM(idapv) = ?', [$apv]))
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

    // Endpoint AJAX pour recharger apv quand faritra change
    public function apvByfaritra(string $idfaritra)
    {
        $rows = DB::table('apv')
            ->selectRaw('TRIM(idapv) as idapv, libelle_apv as libelle_apv, TRIM(idfaritra) as idfaritra')
            ->whereRaw('TRIM(idfaritra) = ?', [trim($idfaritra)])
            ->orderBy('libelle_apv')
            ->get();

        return response()->json($rows);
    }
	
	// Export Excel
	public function exportExcel(Request $request)
	{
		return Excel::download(new FidelesExport($request->all()), 'fideles.xlsx');
	}

	// Export PDF
	public function exportPdf(Request $request)
	{
		ini_set('memory_limit', '512M');
		set_time_limit(120);

		// 🔥 IMPORTANT : chunk au lieu de get massif
		$rows = DB::table('fidele')
			->select('matricule','nom','prenom','nom_bapteme','statut','idfaritra','idapv')
			->orderBy('matricule')
			->limit(5000) // sécurité Render
			->get();

		$pdf = Pdf::loadView('fideles.pdf', [
			'rows' => $rows
		])
		->setPaper('a4', 'landscape')
		->setOption('isHtml5ParserEnabled', true)
		->setOption('isRemoteEnabled', true);

		return $pdf->download('fideles.pdf');
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

    DB::table('fidele')
        ->whereRaw('TRIM(matricule) = ?', [trim($matricule)])
        ->update([
            'nom' => $data['nom'] ?? null,
            'prenom' => $data['prenom'] ?? null,
            'nom_bapteme' => $data['nom_bapteme'] ?? null,
            'statut' => $data['statut'] ?? null,
            'idfaritra' => $data['idfaritra'] ?? null,
            'idapv' => $data['idapv'] ?? null,
        ]);

    return redirect()->route('fideles.index', $request->query())
        ->with('success', 'Fidèle mis à jour avec succès.');
    }
}