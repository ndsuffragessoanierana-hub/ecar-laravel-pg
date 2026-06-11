<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faritra extends Model
{
    protected $table = 'faritra';
    protected $primaryKey = 'idfaritra';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = ['idfaritra', 'libelle_faritra'];

    public function fideles()
    {
        return $this->hasMany(Fidele::class, 'idfaritra', 'idfaritra');
    }
}