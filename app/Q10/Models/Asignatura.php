<?php

namespace App\Q10\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignatura extends Model
{
    use HasFactory;

    /**
     * The table that should be used by the model.
     */
    protected $table = 'asignaturas';

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
}
