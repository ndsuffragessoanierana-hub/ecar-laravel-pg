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
            'hommes' => Fidele::where('sexe', 'L')->count(),
            'femmes' => Fidele::where('sexe', 'V')->count(),
            'actifs' => Fidele::where('statut', 'ACTIF')->count(),
        ];

        $byFaritra = Fidele::query()
            ->leftJoin('faritra', 'faritra.idfaritra', '=', 'fidele.idfaritra')
            ->selectRaw("COALESCE(faritra.libelle_faritra, 'N/A') as faritra, COUNT(fidele.matricule) as total")
            ->groupByRaw("COALESCE(faritra.libelle_faritra, 'N/A')")
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
                    journal_id,
                    (journal_mois || ' ' || journal_annee) AS PERIODE,
                    NVL(journal_solde_bni,0) AS JOURNAL_SOLDE_BNI,
                    NVL(journal_solde_bfv,0) AS JOURNAL_SOLDE_BFV,
                    NVL(journal_solde_caissE,0) AS JOURNAL_SOLDE_CAISSE
                FROM t_journal
                ORDER BY journal_id DESC
            )
            WHERE ROWNUM <= 12
            ORDER BY journal_id ASC
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
                    j.journal_id,
                    (j.journal_mois || ' ' || j.journal_annee) AS PERIODE,
                    SUBSTR(d.rub_rubrique_id,1,1) AS TYPE,
                    NVL(SUM(d.detail_rkp_montant),0) AS MONTANT
                FROM t_journal j
                LEFT JOIN t_detail_recap d
                    ON TRIM(d.rec_rec_mois) = TRIM(j.journal_mois)
                    AND TRIM(d.rec_rec_annee) = TRIM(j.journal_annee)
                WHERE SUBSTR(d.rub_rubrique_id,1,1) IN ('A','B')
                GROUP BY j.journal_id, j.journal_mois, j.journal_annee, substr(d.rub_rubrique_id,1,1)
                ORDER BY j.journal_id DESC
            )
            WHERE ROWNUM <= 24
            ORDER BY journal_id ASC
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