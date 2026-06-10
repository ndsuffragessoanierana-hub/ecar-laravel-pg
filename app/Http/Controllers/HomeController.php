<?php

namespace App\Http\Controllers;

use App\Models\Fidele;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | STATS FIDELES
        |--------------------------------------------------------------------------
        */
        $stats = [
            'total_fideles' => Fidele::count(),
            'hommes' => Fidele::where('SEXE', 'L')->count(),
            'femmes' => Fidele::where('SEXE', 'V')->count(),
            'actifs' => Fidele::where('STATUT', 'ACTIF')->count(),
        ];

        $byFaritra = Fidele::query()
            ->leftJoin('FARITRA', 'FARITRA.IDFARITRA', '=', 'FIDELE.IDFARITRA')
            ->selectRaw("COALESCE(FARITRA.LIBELLE_FARITRA, 'N/A') as faritra, COUNT(FIDELE.MATRICULE) as total")
            ->groupByRaw("COALESCE(FARITRA.LIBELLE_FARITRA, 'N/A')")
            ->pluck('total', 'faritra');

        /*
        |--------------------------------------------------------------------------
        | 1. SOLDES (12 derniers JOURNAL_ID)
        |--------------------------------------------------------------------------
        */
        $journals = DB::select("
            SELECT *
            FROM (
                SELECT
                    JOURNAL_ID,
                    (JOURNAL_MOIS || ' ' || JOURNAL_ANNEE) AS PERIODE,
                    NVL(JOURNAL_SOLDE_BNI,0) AS JOURNAL_SOLDE_BNI,
                    NVL(JOURNAL_SOLDE_BFV,0) AS JOURNAL_SOLDE_BFV,
                    NVL(JOURNAL_SOLDE_CAISSE,0) AS JOURNAL_SOLDE_CAISSE
                FROM T_JOURNAL
                ORDER BY JOURNAL_ID DESC
            )
            WHERE ROWNUM <= 12
            ORDER BY JOURNAL_ID ASC
        ");

        $financeLabels = [];
        $financeBNI = [];
        $financeBFV = [];
        $financeCaisse = [];

        foreach ($journals as $j) {
            $financeLabels[] = $j->periode;

            $financeBNI[] = (float) $j->journal_solde_bni;
            $financeBFV[] = (float) $j->journal_solde_bfv;
            $financeCaisse[] = (float) $j->journal_solde_caisse;
        }


        /*
        |--------------------------------------------------------------------------
        | 2. RECETTES / DEPENSES (12 derniers JOURNAL_ID)
        |--------------------------------------------------------------------------
        */
        $finance2 = DB::select("
            SELECT *
            FROM (
                SELECT
                    j.JOURNAL_ID,
                    (j.JOURNAL_MOIS || ' ' || j.JOURNAL_ANNEE) AS PERIODE,
                    SUBSTR(d.RUB_RUBRIQUE_ID,1,1) AS TYPE,
                    NVL(SUM(d.DETAIL_RKP_MONTANT),0) AS MONTANT
                FROM T_JOURNAL j
                LEFT JOIN T_DETAIL_RECAP d
                    ON TRIM(d.REC_REC_MOIS) = TRIM(j.JOURNAL_MOIS)
                    AND TRIM(d.REC_REC_ANNEE) = TRIM(j.JOURNAL_ANNEE)
                WHERE SUBSTR(d.RUB_RUBRIQUE_ID,1,1) IN ('A','B')
                GROUP BY j.JOURNAL_ID, j.JOURNAL_MOIS, j.JOURNAL_ANNEE, SUBSTR(d.RUB_RUBRIQUE_ID,1,1)
                ORDER BY j.JOURNAL_ID DESC
            )
            WHERE ROWNUM <= 24
            ORDER BY JOURNAL_ID ASC
        ");

        $labels2 = [];
        $recettes = [];
        $depenses = [];

        $map = [];

        foreach ($finance2 as $row) {

            $id = $row->journal_id;

            if (!isset($map[$id])) {
                $map[$id] = [
                    'label' => $row->periode,
                    'A' => 0,
                    'B' => 0,
                ];
            }

            $map[$id][$row->type] = (float) $row->montant;
        }

        foreach ($map as $item) {
            $labels2[] = $item['label'];
            $recettes[] = $item['A'];
            $depenses[] = $item['B'];
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */
        return view('home.index', compact(
            'stats',
            'byFaritra',
            'financeLabels',
            'financeBNI',
            'financeBFV',
            'financeCaisse',
            'labels2',
            'recettes',
            'depenses'
        ));




    }
}