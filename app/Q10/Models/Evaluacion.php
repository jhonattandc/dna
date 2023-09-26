<?php

namespace App\Q10\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    use HasFactory;

    /**
     * The table that should be used by the model.
     */
    protected $table = 'evaluaciones';

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
     * Get the student that owns the evaluation.
     */
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    /**
     * Get the course that owns the evaluation.
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    /**
     * Get the subject that owns the evaluation.
     */
    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class);
    }
}
