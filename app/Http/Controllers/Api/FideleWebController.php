<?php

namespace App\Http\Controllers;

use App\Exports\FidelesExport;
use App\Models\Apv;
use App\Models\Faritra;
use App\Models\Fidele;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FideleWebController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q', ''));
        $faritra = $request->get('faritra');
        $apv = $request->get('apv');

        $query = Fidele::query()->select([
            'MATRICULE', 'NOM', 'PRENOM', 'NOM_BAPTEME',
            'SEXE', 'STATUT', 'IDFARITRA', 'IDAPV'
        ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('MATRICULE', 'like', "%{$search}%")
                  ->orWhere('NOM', 'like', "%{$search}%")
                  ->orWhere('PRENOM', 'like', "%{$search}%");
            });
        }

        if ($faritra) {
            $query->where('IDFARITRA', $faritra);
        }

        if ($apv) {
            $query->where('IDAPV', $apv);
        }

        $fideles = $query->orderBy('IDFARITRA')
            ->orderBy('IDAPV')
            ->orderBy('NOM')
            ->paginate(15)
            ->withQueryString();

        // Dashboard cards
        $stats = [
            'total' => Fidele::count(),
            'hommes' => Fidele::where('SEXE', 'M')->count(),
            'femmes' => Fidele::where('SEXE', 'F')->count(),
            'actifs' => Fidele::where('STATUT', 'ACTIF')->count(),
        ];

        $faritras = Faritra::orderBy('LIBELLE_FARITRA')->get(['IDFARITRA', 'LIBELLE_FARITRA']);
        $apvs = Apv::when($faritra, fn($q) => $q->where('IDFARITRA', $faritra))
            ->orderBy('LIBELLE_APV')
            ->get(['IDAPV', 'LIBELLE_APV', 'IDFARITRA']);

        return view('fideles.index', compact('fideles', 'stats', 'faritras', 'apvs', 'search', 'faritra', 'apv'));
    }

    public function apvByFaritra(string $idfaritra)
    {
        $apvs = Apv::where('IDFARITRA', $idfaritra)
            ->orderBy('LIBELLE_APV')
            ->get(['IDAPV', 'LIBELLE_APV']);

        return response()->json($apvs);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new FidelesExport($request->all()), 'fideles.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $rows = $this->filteredRows($request)->limit(2000)->get();
        $pdf = Pdf::loadView('fideles.pdf', ['rows' => $rows])->setPaper('a4', 'landscape');
        return $pdf->download('fideles.pdf');
    }

    private function filteredRows(Request $request)
    {
        $search = trim((string) $request->get('q', ''));
        $faritra = $request->get('faritra');
        $apv = $request->get('apv');

        $query = Fidele::query()->select([
            'MATRICULE', 'NOM', 'PRENOM', 'NOM_BAPTEME',
            'SEXE', 'STATUT', 'IDFARITRA', 'IDAPV'
        ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('MATRICULE', 'like', "%{$search}%")
                  ->orWhere('NOM', 'like', "%{$search}%")
                  ->orWhere('PRENOM', 'like', "%{$search}%");
            });
        }

        if ($faritra) $query->where('IDFARITRA', $faritra);
        if ($apv) $query->where('IDAPV', $apv);

        return $query->orderBy('IDFARITRA')->orderBy('IDAPV')->orderBy('NOM');
    }
}