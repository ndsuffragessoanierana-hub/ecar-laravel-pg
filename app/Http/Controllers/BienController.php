<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BienController extends Controller
{
    /**
     * 🔹 Affichage de la liste des biens (page web)
     */
    public function index()
    {
        // 🔸 Requête principale
        $biens = DB::select("
            SELECT 
                f.idfitaovana, 
                f.denomination, 
                f.reference, 
                f.date_acquisition, 
                f.valeur_acquisition, 
                f.qte_achetee, 
                p.libelle_place, 
                p.toerana
            FROM fitaovana f
            LEFT JOIN empla_fitaovana e ON e.fit_idfitaovana = f.idfitaovana 
            LEFT JOIN emplacement p ON p.idplace = e.emp_idplace
            ORDER BY p.toerana
        ");

        // 🔸 Envoi des données à la vue
        return view('biens.index', compact('biens'));
    }

    /**
     * 🔹 Génération du PDF
     */
    public function pdf()
    {
        // 🔸 Même requête que index
        $biens = DB::select("
            SELECT 
                f.idfitaovana, 
                f.denomination, 
                f.reference, 
                f.date_acquisition, 
                f.valeur_acquisition, 
                f.qte_achetee, 
                p.libelle_place, 
                p.toerana
            FROM fitaovana f
            LEFT JOIN empla_fitaovana e ON e.fit_idfitaovana = f.idfitaovana 
            LEFT JOIN emplacement p ON p.idplace = e.emp_idplace
            ORDER BY p.toerana
        ");

        // 🔸 Création du PDF
        $pdf = Pdf::loadView('biens.pdf', [
                'biens' => $biens
            ])
            ->setPaper('A4', 'landscape'); // paysage

        // 🔸 Téléchargement du fichier
        return $pdf->download('liste_biens.pdf');
    }

//  PAGE AVEC QR CODE
    public function qrcode()
    {
        $biens = DB::select("
            SELECT 
            F.IDFITAOVANA,
            F.DENOMINATION,
            F.REFERENCE,
            F.REMARQUE,
            F.QR_TEXT,
            LF.NO_INVENTAIRE,
            LF.LOCALISATION
            FROM FITAOVANA F
            LEFT JOIN LISTE_FITAOVANA LF 
            ON LF.IDFITAOVANA = F.IDFITAOVANA
        ");

        return view('biens.qrcode', compact('biens'));
    }


//  PDF AVEC QR CODE
/*
    public function qrcodePdf()
    {
        $biens = DB::select("
            SELECT 
                f.idfitaovana, 
                f.denomination, 
                f.reference, 
                f.qr_text,
                p.toerana, 
                p.libelle_place,
                lf.no_inventaire,
                f.qr_code
            FROM fitaovana f
            LEFT JOIN empla_fitaovana e ON e.fit_idfitaovana = f.idfitaovana 
            LEFT JOIN emplacement p ON p.idplace = e.emp_idplace
            LEFT JOIN LISTE_FITAOVANA LF ON LF.IDFITAOVANA = F.IDFITAOVANA
            ORDER BY p.toerana FETCH FIRST 300 ROWS ONLY
        ");

        $pdf = Pdf::loadView('biens.qrcode_pdf', compact('biens'))
                ->setPaper('A4', 'portrait');

        $pdf->getDomPDF()->set_option("isPhpEnabled", true);

        return $pdf->download('biens_qrcode.pdf');
    }
*/

    public function qrcodePdf($page = 1)
    {
        $limit = 100;
        $offset = ($page - 1) * $limit;

        $biens = DB::select("
            SELECT 
                f.idfitaovana, 
                f.denomination, 
                f.reference, 
                f.qr_text,
                p.toerana, 
                p.libelle_place,
                lf.no_inventaire,
                f.qr_code
            FROM fitaovana f
            LEFT JOIN empla_fitaovana e ON e.fit_idfitaovana = f.idfitaovana 
            LEFT JOIN emplacement p ON p.idplace = e.emp_idplace
            LEFT JOIN LISTE_FITAOVANA LF ON LF.IDFITAOVANA = F.IDFITAOVANA
            ORDER BY p.toerana
            OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY
        ");

        $pdf = Pdf::loadView('biens.qrcode_pdf', compact('biens'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream("biens_qrcode_page_$page.pdf");
    }

}