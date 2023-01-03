<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
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
        'Fecha_fin' => 'datetime',
        'Estado' => 'boolean'
    ];

    /**
     * Get the courses for the term.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the campus that owns the term.
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }
}
