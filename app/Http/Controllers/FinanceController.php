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
        $journaux = DB::select("
            SELECT journal_id, journal_mois, journal_annee
            FROM t_journal
            ORDER BY journal_id DESC
        ");

        $journalId = $request->get('journal_id') ?? ($journaux[0]->journal_id ?? null);

        $details = [];

        if ($journalId) {
            $details = DB::select("
                SELECT
                    j_detail_mode_paie,
                    j_detail_date,
                    j_detail_libelle,
                    j_detail_montant,
                    j_detail_numero,
                    rub_rubrique_id,
                    jrl_journal_id,

                    CASE WHEN rub_rubrique_id LIKE 'A%' OR rub_rubrique_id IN ('69999','502','503')
                        THEN j_detail_montant END AS recette_g,

                    CASE WHEN rub_rubrique_id LIKE 'B%' OR rub_rubrique_id IN ('79999','501','502')
                        THEN j_detail_montant END AS depense_g,

                    CASE WHEN j_detail_mode_paie='ESP' AND (rub_rubrique_id LIKE 'A%' OR rub_rubrique_id='69999')
                        THEN j_detail_montant END AS recette_num,

                    CASE WHEN (j_detail_mode_paie='ESP' AND rub_rubrique_id LIKE 'B%')
                        THEN j_detail_montant END AS depense_num,

                    CASE WHEN j_detail_mode_paie='BFV' AND (rub_rubrique_id LIKE 'A%' OR rub_rubrique_id IN ('69999','502','503'))
                        THEN j_detail_montant END AS recette_bfv,

                    CASE WHEN j_detail_mode_paie='BFV' AND (rub_rubrique_id LIKE 'B%' OR rub_rubrique_id IN ('79999','501'))
                        THEN j_detail_montant END AS depense_bfv,

                    CASE WHEN j_detail_mode_paie='BNI' AND (rub_rubrique_id LIKE 'A%' OR rub_rubrique_id IN ('69999','502','503'))
                        THEN j_detail_montant END AS recette_bni,

                    CASE WHEN j_detail_mode_paie='BNI' AND (rub_rubrique_id LIKE 'B%' OR rub_rubrique_id IN ('79999','501'))
                        THEN j_detail_montant END AS depense_bni

                FROM t_detail_journal
                WHERE jrl_journal_id = :journalId
                ORDER BY j_detail_date, j_detail_numero
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

        $details = DB::select("
            SELECT
                j_detail_mode_paie,
                j_detail_date,
                j_detail_libelle,
                j_detail_montant,
                j_detail_numero,
                rub_rubrique_id,
                jrl_journal_id,

                CASE WHEN rub_rubrique_id LIKE 'A%' OR rub_rubrique_id IN ('69999','502','503')
                    THEN j_detail_montant END AS recette_g,

                CASE WHEN rub_rubrique_id LIKE 'B%' OR rub_rubrique_id IN ('79999','501','502')
                    THEN j_detail_montant END AS depense_g,

                CASE WHEN j_detail_mode_paie='ESP' AND (rub_rubrique_id LIKE 'A%' OR rub_rubrique_id='69999')
                    THEN j_detail_montant END AS recette_num,

                CASE WHEN (j_detail_mode_paie='ESP' AND rub_rubrique_id LIKE 'B%')
                    THEN j_detail_montant END AS depense_num,

                CASE WHEN j_detail_mode_paie='BFV' AND (rub_rubrique_id LIKE 'A%' OR rub_rubrique_id IN ('69999','502','503'))
                    THEN j_detail_montant END AS recette_bfv,

                CASE WHEN j_detail_mode_paie='BFV' AND (rub_rubrique_id LIKE 'B%' OR rub_rubrique_id IN ('79999','501'))
                    THEN j_detail_montant END AS depense_bfv,

                CASE WHEN j_detail_mode_paie='BNI' AND (rub_rubrique_id LIKE 'A%' OR rub_rubrique_id IN ('69999','502','503'))
                    THEN j_detail_montant END AS recette_bni,

                CASE WHEN j_detail_mode_paie='BNI' AND (rub_rubrique_id LIKE 'B%' OR rub_rubrique_id IN ('79999','501'))
                    THEN j_detail_montant END AS depense_bni

            FROM t_detail_journal
            WHERE jrl_journal_id = :journalId
            ORDER BY j_detail_date, j_detail_numero
        ", ['journalId' => $journalId]);

        $journal = DB::selectOne("
            SELECT journal_mois, journal_annee
            FROM t_journal
            WHERE journal_id = :journalId
        ", ['journalId' => $journalId]);

        $periode = $journal ? ($journal->journal_mois . ' ' . $journal->journal_annee) : '';

        $recap = DB::select("
            SELECT
                r.rubrique_libelle,
                r.rubrique_id,

                CASE WHEN d.rub_rubrique_id LIKE 'A%' OR d.rub_rubrique_id = '503'
                    THEN d.detail_rkp_montant END AS recette,

                CASE WHEN d.rub_rubrique_id LIKE 'B%' OR d.rub_rubrique_id = '501'
                    THEN d.detail_rkp_montant END AS depense

            FROM t_detail_recap d
            JOIN t_rubrique r ON d.rub_rubrique_id = r.rubrique_id

            WHERE d.detail_rkp_montant > 0
              AND d.rub_rubrique_id NOT IN ('502','69999','79999')
              AND d.rec_rec_annee = (SELECT journal_annee FROM t_journal WHERE journal_id = :journalId)
              AND d.rec_rec_mois = (SELECT journal_mois FROM t_journal WHERE journal_id = :journalId)

            ORDER BY d.rub_rubrique_id
        ", ['journalId' => $journalId]);

        $soldeCourant = DB::selectOne("
            SELECT
                journal_solde_bni,
                journal_solde_bfv,
                journal_solde_caisse,
                (journal_solde_bni + journal_solde_bfv + journal_solde_caisse) AS total
            FROM t_journal
            WHERE journal_id = :id
        ", ['id' => $journalId]);

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

        $soldeCourant = $soldeCourant ?? (object)[
            'journal_solde_bni' => 0,
            'journal_solde_bfv' => 0,
            'journal_solde_caisse' => 0,
            'total' => 0
        ];

        $soldePrecedent = $soldePrecedent ?? (object)[
            'journal_solde_bni' => 0,
            'journal_solde_bfv' => 0,
            'journal_solde_caisse' => 0,
            'total' => 0
        ];

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
     * 🔁 DETAIL PAR COMPTE (POSTGRESQL CLEAN)
     */
    private function getDetailData($noCompte, $dateDebut, $dateFin)
    {
        $rows = collect();
        $totalRecette = 0;
        $totalDepense = 0;
        $solde = 0;

        if ($noCompte && $dateDebut && $dateFin) {

            $rows = DB::table('t_detail_journal')
                ->selectRaw("
                    j_detail_mode_paie,
                    j_detail_date,
                    j_detail_libelle,
                    j_detail_montant,
                    j_detail_numero,
                    rub_rubrique_id,
                    jrl_journal_id,

                    CASE WHEN rub_rubrique_id LIKE 'A%' OR rub_rubrique_id IN ('69999','502','503')
                        THEN j_detail_montant END AS recette_g,

                    CASE WHEN rub_rubrique_id LIKE 'B%' OR rub_rubrique_id IN ('79999','501')
                        THEN j_detail_montant END AS depense_g
                ")
                ->where('cpt_no_compte', $noCompte)
                ->whereBetween('j_detail_date', [
                    Carbon::parse($dateDebut)->toDateString(),
                    Carbon::parse($dateFin)->toDateString()
                ])
                ->orderBy('j_detail_date')
                ->orderBy('j_detail_numero')
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

        $comptes = DB::table('compte')
            ->selectRaw('no_compte, libelle_compte')
            ->orderBy('no_compte')
            ->get();

        $data = $this->getDetailData($noCompte, $dateDebut, $dateFin);

        return view('finances.detail-par-compte', array_merge($data, [
            'comptes' => $comptes,
            'filters' => compact('noCompte', 'dateDebut', 'dateFin'),
        ]));
    }

    public function detailParComptePdf(Request $request)
    {
        $noCompte = trim((string) $request->get('compte', ''));
        $dateDebut = trim((string) $request->get('date_debut', ''));
        $dateFin = trim((string) $request->get('date_fin', ''));

        $comptes = DB::table('compte')->get();

        $data = $this->getDetailData($noCompte, $dateDebut, $dateFin);

        $libelleCompte = optional(
            $comptes->firstWhere('no_compte', $noCompte)
        )->libelle_compte;

        return Pdf::loadView('finances.detail-par-compte-pdf', array_merge($data, [
            'compte' => $noCompte,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'libelleCompte' => $libelleCompte,
        ]))->setPaper('a4', 'landscape')
          ->download('detail-par-compte.pdf');
    }
}