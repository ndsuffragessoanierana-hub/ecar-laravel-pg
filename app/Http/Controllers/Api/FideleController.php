<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fidele;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FideleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Fidele::query()
            ->select([
                'MATRICULE',
                'NOM',
                'PRENOM',
                'NOM_BAPTEME',
                'DATE_NAISSANCE',
                'ADRESSE',
                'SEXE',
                'STATUT',
                'IDFARITRA',
                'IDAPV',
            ])
            ->orderBy('IDFARITRA')
            ->orderBy('IDAPV')
            ->orderBy('MATRICULE');

        if ($request->filled('idfari tra')) { // facultatif, filtre
            $query->where('IDFARITRA', $request->string('idfaritra'));
        }

        if ($request->filled('idapv')) { // facultatif, filtre
            $query->where('IDAPV', $request->string('idapv'));
        }

        return response()->json($query->limit(200)->get());
    }
}