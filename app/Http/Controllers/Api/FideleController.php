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
                'matricule',
                'nom',
                'prenom',
                'nom_bapteme',
                'date_naissance',
                'adresse',
                'sexe',
                'statut',
                'idfaritra',
                'idapv',
            ])
            ->orderBy('idfaritra')
            ->orderBy('idapv')
            ->orderBy('matricule');

        if ($request->filled('idfaritra')) { // facultatif, filtre
            $query->where('idfaritra', $request->string('idfaritra'));
        }

        if ($request->filled('idapv')) { // facultatif, filtre
            $query->where('idapv', $request->string('idapv'));
        }

        return response()->json($query->limit(200)->get());
    }
}