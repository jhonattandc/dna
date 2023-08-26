<?php

namespace App\Q10\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    /**
     * The table that should be used by the model.
     */
    protected $table = 'cursos';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer'
    ];

    /**
     * Get the term that owns the course.
     */
    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    /**
     * Get the program that owns the course.
     */
    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    /**
     * Get the jornada_sede that owns the course.
     */
    public function jornada_sede()
    {
        return $this->belongsTo(JornadaSede::class);
    }

    /**
     * Get the docente that owns the course.
     */
    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }
}
