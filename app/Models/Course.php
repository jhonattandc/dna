<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

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
        'Fecha_inicio' => 'datetime',
        'Fecha_fin' => 'datetime'
    ];

    /**
     * Get the term that owns the course.
     */
    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Get the evaluations for the course.
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
