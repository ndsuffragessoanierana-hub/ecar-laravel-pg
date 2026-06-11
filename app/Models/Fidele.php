<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fidele extends Model
{
    protected $table = 'fidele';          // Nom table Oracle
    protected $primaryKey = 'MATRICULE';  // Ajuste si autre PK
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    // Colonnes de base (ajuste selon ta table)
    protected $fillable = [
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
    ];
}