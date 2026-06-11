<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apv extends Model
{
    protected $table = 'apv';
    protected $primaryKey = 'idapv';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = ['idapv', 'libelle_apv', 'idfaritra'];
}