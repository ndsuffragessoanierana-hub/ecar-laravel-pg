<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceController extends Controller
{
    /**
     * 🔹 LIVRE JOURNAL (PAGE)
     */
    public function livreJournal(Request $request)
    {
        // 🔹 Liste des journaux
        $journaux = DB::select("
            SELECT journal_id, journal_mois, journal_annee
            FROM t_journal
            ORDER BY journal_id DESC
        ");

        // 🔹 Journal sélectionné
        $journalId = $request->get('journal_id') ?? ($journaux[0]->journal_id ?? null);

        // 🔹 Détails
        $details = [];

        if ($journalId) {
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
        }

        return view('finances.livre-journal', compact('details', 'journalId', 'journaux'));
    }

    /**
     * 🔹 PDF LIVRE JOURNAL
     */
    public function livreJournalPdf(Request $request)
    {
        $journalId = $request->get('journal_id');

        // 🔹 Détails du journal
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

        // 🔹 Période
        $journal = DB::selectOne("
            SELECT journal_mois, journal_annee 
            FROM t_journal 
            WHERE journal_id = :journalId
        ", ['journalId' => $journalId]);

        $periode = $journal ? ($journal->journal_mois . ' ' . $journal->journal_annee) : '';

        // 🔹 Récap
        $recap = DB::select("
            SELECT 
                T_RUBRIQUE.RUBRIQUE_LIBELLE, 
                T_RUBRIQUE.RUBRIQUE_ID,
                CASE WHEN T_DETAIL_RECAP.RUB_RUBRIQUE_ID LIKE 'A%' OR T_DETAIL_RECAP.RUB_RUBRIQUE_ID = '503' 
                    THEN T_DETAIL_RECAP.DETAIL_RKP_MONTANT END AS recette,
                CASE WHEN T_DETAIL_RECAP.RUB_RUBRIQUE_ID LIKE 'B%' OR T_DETAIL_RECAP.RUB_RUBRIQUE_ID = '501'  
                    THEN T_DETAIL_RECAP.DETAIL_RKP_MONTANT END AS depense
            FROM T_DETAIL_RECAP, T_RUBRIQUE
            WHERE T_DETAIL_RECAP.RUB_RUBRIQUE_ID = T_RUBRIQUE.RUBRIQUE_ID
                AND T_DETAIL_RECAP.DETAIL_RKP_MONTANT > 0
                AND T_DETAIL_RECAP.RUB_RUBRIQUE_ID NOT IN ('502','69999','79999')
                AND TRIM(T_DETAIL_RECAP.REC_REC_ANNEE) = (
                    SELECT journal_annee FROM t_journal WHERE journal_id = :journalId
                )
                AND TRIM(T_DETAIL_RECAP.REC_REC_MOIS) = (
                    SELECT TRIM(journal_mois) FROM t_journal WHERE journal_id = :journalId
                )
            ORDER BY T_DETAIL_RECAP.RUB_RUBRIQUE_ID
        ", ['journalId' => $journalId]);

        // 🔹 SOLDE COURANT
        $soldeCourant = DB::selectOne("
            SELECT 
                journal_solde_bni,
                journal_solde_bfv,
                journal_solde_caisse,
                (journal_solde_bni + journal_solde_bfv + journal_solde_caisse) AS total
            FROM t_journal
            WHERE journal_id = :id
        ", ['id' => $journalId]);

        // 🔹 SOLDE PRECEDENT (PROPRE)
        $soldePrecedent = DB::selectOne("
            SELECT 
                journal_solde_bni,
                journal_solde_bfv,
                journal_solde_caisse,
                (journal_solde_bni + journal_solde_bfv + journal_solde_caisse) AS total
            FROM t_journal
            WHERE journal_id = (
                SELECT MAX(journal_id)
                FROM t_journal
                WHERE journal_id < :id
            )
        ", ['id' => $journalId]);

        // 🔹 Sécurité
        $soldeCourant = $soldeCourant ?? (object)[
            'journal_solde_bni'=>0,
            'journal_solde_bfv'=>0,
            'journal_solde_caisse'=>0,
            'total'=>0
        ];

        $soldePrecedent = $soldePrecedent ?? (object)[
            'journal_solde_bni'=>0,
            'journal_solde_bfv'=>0,
            'journal_solde_caisse'=>0,
            'total'=>0
        ];

        // 🔹 PDF
        $pdf = Pdf::loadView('finances.livre-journal-pdf', compact(
            'details',
            'periode',
            'recap',
            'soldeCourant',
            'soldePrecedent'
        ));

        return $pdf->stream('livre_journal.pdf');
    }

    
    /**
     * 🔁 FONCTION COMMUNE (DETAIL PAR COMPTE)
     */
    private function getDetailData($noCompte, $dateDebut, $dateFin)
    {
        $rows = collect();
        $totalRecette = 0.0;
        $totalDepense = 0.0;
        $solde = 0.0;

        if ($noCompte !== '' && $dateDebut !== '' && $dateFin !== '') {
            $ddmmyyyyDebut = Carbon::parse($dateDebut)->format('d/m/Y');
            $ddmmyyyyFin = Carbon::parse($dateFin)->format('d/m/Y');

            $rows = DB::table('T_DETAIL_JOURNAL')
                ->selectRaw("
                    J_DETAIL_MODE_PAIE,
                    J_DETAIL_DATE,
                    J_DETAIL_LIBELLE,
                    J_DETAIL_MONTANT,
                    J_DETAIL_NUMERO,
                    RUB_RUBRIQUE_ID,
                    JRL_JOURNAL_ID,
                    CASE WHEN RUB_RUBRIQUE_ID LIKE 'A%' OR RUB_RUBRIQUE_ID IN ('69999','502','503') THEN J_DETAIL_MONTANT END as recette_g,
                    CASE WHEN RUB_RUBRIQUE_ID LIKE 'B%' OR RUB_RUBRIQUE_ID IN ('79999','501') THEN J_DETAIL_MONTANT END as depense_g
                ")
                ->whereRaw('TRIM(CPT_NO_COMPTE) = ?', [$noCompte])
                ->whereRaw(
                    "J_DETAIL_DATE BETWEEN TO_DATE(?, 'DD/MM/RRRR') AND TO_DATE(?, 'DD/MM/RRRR')",
                    [$ddmmyyyyDebut, $ddmmyyyyFin]
                )
                ->orderBy('J_DETAIL_DATE')
                ->orderBy('J_DETAIL_NUMERO')
                ->get();

            $totalRecette = (float) $rows->sum(fn ($r) => (float) ($r->recette_g ?? 0));
            $totalDepense = (float) $rows->sum(fn ($r) => (float) ($r->depense_g ?? 0));
            $solde = $totalRecette - $totalDepense;
        }

        return compact('rows', 'totalRecette', 'totalDepense', 'solde');
    }

    public function detailParCompte(Request $request)
    {
        $noCompte = trim((string) $request->get('compte', ''));
        $dateDebut = trim((string) $request->get('date_debut', ''));
        $dateFin = trim((string) $request->get('date_fin', ''));

        $comptes = DB::table('COMPTE')
            ->selectRaw('TRIM(NO_COMPTE) as no_compte, LIBELLE_COMPTE as libelle_compte')
            ->orderBy('NO_COMPTE')
            ->get();

        $data = $this->getDetailData($noCompte, $dateDebut, $dateFin);

        return view('finances.detail-par-compte', array_merge($data, [
            'comptes' => $comptes,
            'filters' => compact('noCompte','dateDebut','dateFin'),
        ]));
    }

    public function detailParComptePdf(Request $request)
    {
        $noCompte = trim((string) $request->get('compte', ''));
        $dateDebut = trim((string) $request->get('date_debut', ''));
        $dateFin = trim((string) $request->get('date_fin', ''));

        $comptes = DB::table('COMPTE')->get();

        $data = $this->getDetailData($noCompte, $dateDebut, $dateFin);

        $libelleCompte = optional(
            $comptes->firstWhere('NO_COMPTE', $noCompte)
        )->LIBELLE_COMPTE;

        return Pdf::loadView('finances.detail-par-compte-pdf', array_merge($data, [
            'compte' => $noCompte,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'libelleCompte' => $libelleCompte,
        ]))->setPaper('a4', 'landscape')
          ->download('detail-par-compte.pdf');
    }
}