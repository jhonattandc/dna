<?php

namespace App\Q10\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    use HasFactory;

    /**
     * The table that should be used by the model.
     */
    protected $table = 'programas';

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
        'id' => 'integer',
        'campus_id' => 'integer',
    ];

    /**
     * Get the campus that owns the programa.
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * Get the cursos that owns the programa.
     */
    public function cursos()
    {
        return $this->hasMany(Curso::class);
    }
}
