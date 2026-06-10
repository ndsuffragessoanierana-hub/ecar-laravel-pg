<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apv extends Model
{
    protected $table = 'APV';
    protected $primaryKey = 'IDAPV';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = ['IDAPV', 'LIBELLE_APV', 'IDFARITRA'];
}