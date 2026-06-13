<?php

use App\Http\Controllers\FinanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BienController;
use App\Http\Controllers\Api\FideleWebController;
use App\Http\Controllers\JournalController;

// Accueil
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/accueil', [HomeController::class, 'index'])->name('accueil');

// Fidèles
Route::prefix('fideles')->name('fideles.')->group(function () {
    Route::get('/', [FideleWebController::class, 'index'])->name('index');
    Route::put('/{matricule}', [FideleWebController::class, 'update'])->name('update');

    Route::get('/export/excel', [FideleWebController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/pdf', [FideleWebController::class, 'exportPdf'])->name('export.pdf');

    Route::get('/api/apv-by-faritra/{idfaritra}', [FideleWebController::class, 'apvByFaritra'])->name('apv.by.faritra');
});

// Pages statiques
Route::view('/biens', 'biens.index')->name('biens.index');
Route::view('/finances', 'finances.index')->name('finances.index');
Route::view('/parametres', 'parametres.index')->name('parametres.index');

// Finances
Route::prefix('finances')->name('finances.')->group(function () {
    Route::get('/livre-journal', [FinanceController::class, 'livreJournal'])->name('livre_journal');
    Route::get('/livre-journal/pdf', [FinanceController::class, 'livreJournalPdf'])->name('livre_journal_pdf'); // ✅ PDF

    Route::get('/detail-par-compte', [FinanceController::class, 'detailParCompte'])->name('detail_compte');
    Route::get('/detail-par-compte/pdf', [FinanceController::class, 'detailParComptePdf'])->name('detail_compte_pdf');
});

Route::get('/journal/{journalId}', [JournalController::class, 'index'])->name('journal.index');
Route::get('/journal/{journalId}/pdf', [JournalController::class, 'pdf'])->name('journal.pdf');

Route::get('/biens', [BienController::class, 'index'])->name('biens.index');
Route::get('/biens/pdf', [BienController::class, 'pdf'])->name('biens.pdf');

Route::get('/biens/qrcode', [BienController::class, 'qrcode'])->name('biens.qrcode');
// Route::get('/biens/qrcode/pdf/{page?}', [BienController::class, 'qrcodePdf']);
// Route::get('/biens/qrcode/pdf', [BienController::class, 'qrcodePdf'])->name('biens.qrcode.pdf');

Route::get('/biens/qrcode/pdf/{page?}', [BienController::class, 'qrcodePdf'])
    ->name('biens.qrcode.pdf');

//Route::get('/biens/qrcode/pdf', [BienController::class, 'qrcodePdf']);