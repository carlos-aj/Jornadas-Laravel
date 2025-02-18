<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eventos extends Model
{
    use HasFactory;

    protected $table = 'eventos';

    protected $fillable = [
        'titulo',
        'tipo',
        'fecha',
        'hora',
        'cupo_maximo',
        'ponente_id',
    ];

    public function ponente()
    {
        return $this->belongsTo(Ponente::class);
    }
}
