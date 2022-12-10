<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
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
        'Fecha_resolucion' => 'datetime',
        'Aplica_preinscripcion' => 'boolean',
        'Aplica_grupo' => 'boolean',
        'Estado' => 'boolean'
    ];


    /**
     * Get the campus that owns the program.
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * The student that belong to the timetable.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class);
    }
}
