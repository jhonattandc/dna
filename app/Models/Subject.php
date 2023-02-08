<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
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
     * Get the evaluations for the course.
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    /**
     * Get the students that owns the course.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class);
    }

    /**
     * Get the tkcourse that owns the course.
     */
    public function tkcourse()
    {
        return $this->belongsTo(Tkcourse::class, 'course_tk_id', 'course_id');
    }
}
