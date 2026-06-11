<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faritra extends Model
{
    protected $table = 'faritra';
    protected $primaryKey = 'IDFARITRA';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = ['IDFARITRA', 'LIBELLE_FARITRA'];

    public function fideles()
    {
        return $this->hasMany(Fidele::class, 'IDFARITRA', 'IDFARITRA');
    }
}