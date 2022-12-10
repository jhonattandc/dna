<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
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
        'Fecha_nacimiento' => 'datetime',
        'Fecha_matricula' => 'datetime'
    ];

    /**
     * The programs that belong to the students.
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class);
    }

    /**
     * The programs that belong to the students.
     */
    public function terms()
    {
        return $this->belongsToMany(Term::class);
    }

    /**
     * The programs that belong to the students.
     */
    public function timetable()
    {
        return $this->belongsToMany(Timetable::class);
    }
}
