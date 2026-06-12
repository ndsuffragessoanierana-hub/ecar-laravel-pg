<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class FidelesExport implements FromCollection
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        return DB::table('fidele')
            ->select('matricule','nom','prenom','nom_bapteme','statut','idfaritra','idapv')
            ->orderBy('matricule')
            ->get();
    }
}