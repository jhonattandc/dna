<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    use HasFactory;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'Secreto',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'Secreto' => 'encrypted',
        'last_sync_at' => 'datetime',
    ];

    /**
     * Get the prgorams for the campus.
     */
    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    /**
     * Get the terms for the campus.
     */
    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    /**
     * Get the prgorams for the campus.
     */
    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
}
