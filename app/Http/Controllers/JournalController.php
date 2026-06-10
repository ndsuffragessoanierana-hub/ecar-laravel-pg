<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB; // pour Oracle
use PDF; // barryvdh/laravel-dompdf

class JournalController extends Controller
{
    public function index($journalId)
    {
        // Récupération des données du journal
        $details = DB::select("
            SELECT J_DETAIL_MODE_PAIE, J_DETAIL_DATE, J_DETAIL_LIBELLE,
                   J_DETAIL_MONTANT, J_DETAIL_NUMERO, RUB_RUBRIQUE_ID, JRL_JOURNAL_ID,
                   CASE WHEN RUB_RUBRIQUE_ID LIKE 'A%' OR RUB_RUBRIQUE_ID IN ('69999','502','503') THEN J_DETAIL_MONTANT END AS recette_g,
                   CASE WHEN RUB_RUBRIQUE_ID LIKE 'B%' OR RUB_RUBRIQUE_ID IN ('79999','501','502') THEN J_DETAIL_MONTANT END AS depense_g,
                   CASE WHEN J_DETAIL_MODE_PAIE='ESP' AND (RUB_RUBRIQUE_ID LIKE 'A%' OR RUB_RUBRIQUE_ID='69999') THEN J_DETAIL_MONTANT END AS recette_num,
                   CASE WHEN (J_DETAIL_MODE_PAIE='ESP' AND RUB_RUBRIQUE_ID LIKE 'B%') OR (J_DETAIL_MODE_PAIE LIKE 'B%' AND RUB_RUBRIQUE_ID='502') THEN J_DETAIL_MONTANT END AS depense_num,
                   CASE WHEN J_DETAIL_MODE_PAIE='BFV' AND (RUB_RUBRIQUE_ID LIKE 'A%' OR RUB_RUBRIQUE_ID IN ('69999','502','503')) THEN J_DETAIL_MONTANT END AS recette_bfv,
                   CASE WHEN J_DETAIL_MODE_PAIE='BFV' AND (RUB_RUBRIQUE_ID LIKE 'B%' OR RUB_RUBRIQUE_ID IN ('79999','501')) THEN J_DETAIL_MONTANT END AS depense_bfv,
                   CASE WHEN J_DETAIL_MODE_PAIE='BNI' AND (RUB_RUBRIQUE_ID LIKE 'A%' OR RUB_RUBRIQUE_ID IN ('69999','502','503')) THEN J_DETAIL_MONTANT END AS recette_bni,
                   CASE WHEN J_DETAIL_MODE_PAIE='BNI' AND (RUB_RUBRIQUE_ID LIKE 'B%' OR RUB_RUBRIQUE_ID IN ('79999','501')) THEN J_DETAIL_MONTANT END AS depense_bni
            FROM T_DETAIL_JOURNAL
            WHERE JRL_JOURNAL_ID = :journalId
            ORDER BY J_DETAIL_DATE, J_DETAIL_NUMERO
        ", ['journalId' => $journalId]);

        return view('journal.index', compact('details', 'journalId'));
    }

    public function pdf($journalId)
    {
        $details = DB::select("
            SELECT J_DETAIL_MODE_PAIE, J_DETAIL_DATE, J_DETAIL_LIBELLE,
                   J_DETAIL_MONTANT, J_DETAIL_NUMERO, RUB_RUBRIQUE_ID, JRL_JOURNAL_ID,
                   CASE WHEN RUB_RUBRIQUE_ID LIKE 'A%' OR RUB_RUBRIQUE_ID IN ('69999','502','503') THEN J_DETAIL_MONTANT END AS recette_g,
                   CASE WHEN RUB_RUBRIQUE_ID LIKE 'B%' OR RUB_RUBRIQUE_ID IN ('79999','501','502') THEN J_DETAIL_MONTANT END AS depense_g,
                   CASE WHEN J_DETAIL_MODE_PAIE='ESP' AND (RUB_RUBRIQUE_ID LIKE 'A%' OR RUB_RUBRIQUE_ID='69999') THEN J_DETAIL_MONTANT END AS recette_num,
                   CASE WHEN (J_DETAIL_MODE_PAIE='ESP' AND RUB_RUBRIQUE_ID LIKE 'B%') OR (J_DETAIL_MODE_PAIE LIKE 'B%' AND RUB_RUBRIQUE_ID='502') THEN J_DETAIL_MONTANT END AS depense_num,
                   CASE WHEN J_DETAIL_MODE_PAIE='BFV' AND (RUB_RUBRIQUE_ID LIKE 'A%' OR RUB_RUBRIQUE_ID IN ('69999','502','503')) THEN J_DETAIL_MONTANT END AS recette_bfv,
                   CASE WHEN J_DETAIL_MODE_PAIE='BFV' AND (RUB_RUBRIQUE_ID LIKE 'B%' OR RUB_RUBRIQUE_ID IN ('79999','501')) THEN J_DETAIL_MONTANT END AS depense_bfv,
                   CASE WHEN J_DETAIL_MODE_PAIE='BNI' AND (RUB_RUBRIQUE_ID LIKE 'A%' OR RUB_RUBRIQUE_ID IN ('69999','502','503')) THEN J_DETAIL_MONTANT END AS recette_bni,
                   CASE WHEN J_DETAIL_MODE_PAIE='BNI' AND (RUB_RUBRIQUE_ID LIKE 'B%' OR RUB_RUBRIQUE_ID IN ('79999','501')) THEN J_DETAIL_MONTANT END AS depense_bni
            FROM T_DETAIL_JOURNAL
            WHERE JRL_JOURNAL_ID = :journalId
            ORDER BY J_DETAIL_DATE, J_DETAIL_NUMERO
        ", ['journalId' => $journalId]);

        $pdf = PDF::loadView('journal.pdf', compact('details'));
        return $pdf->stream("livre_journal_{$journalId}.pdf");
    }



}