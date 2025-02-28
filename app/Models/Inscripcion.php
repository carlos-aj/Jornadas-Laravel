<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripcion';

    protected $fillable = [
        'user_id',
        'evento_id',
        'tipo_inscripcion',
    ];

    public function evento()
    {
        return $this->belongsTo(Eventos::class, 'evento_id');
    }
}
