<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fidele extends Model
{
    protected $table = 'fidele';          // Nom table Oracle
    protected $primaryKey = 'matricule';  // Ajuste si autre PK
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    // Colonnes de base (ajuste selon ta table)
    protected $fillable = [
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
    ];
}