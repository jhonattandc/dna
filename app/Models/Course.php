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
     * Get the tkcourse that owns the course.
     */
    public function tkcourse()
    {
        return $this->belongsTo(Tkcourse::class, 'course_tk_id', 'course_id');
    }

    /**
     * Get the program that owns the course.
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class);
    }

    /**
     * Get the students that owns the course.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class);
    }

    /**
     * Get the evaluations for the course.
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
