<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apv extends Model
{
    protected $table = 'apv';
    protected $primaryKey = 'IDAPV';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = ['IDAPV', 'LIBELLE_APV', 'IDFARITRA'];
}