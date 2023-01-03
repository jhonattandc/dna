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
     * The courses that belong to the students.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }
}
