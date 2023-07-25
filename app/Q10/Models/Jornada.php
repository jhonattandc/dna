<?php

namespace App\Q10\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jornada extends Model
{
    use HasFactory;

    /**
     * The table that should be used by the model.
     */
    protected $table = 'jornadas';

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
     * Get the campus that owns the sede.
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * Get the sedes for the jornada.
     */
    public function sedes()
    {
        return $this->belongsToMany(Sede::class)->withPivot('Consecutivo', 'Estado')->withTimestamps();
    }
}
