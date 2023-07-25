<?php

namespace App\Q10\Models;

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
     * Get the roles for the campus.
     */
    public function roles()
    {
        return $this->hasMany(Rol::class);
    }

    /**
     * Get the perfiles for the campus.
     */
    public function perfiles()
    {
        return $this->hasMany(Perfil::class);
    }

    /**
     * Get the tipo_id for the campus.
     */
    public function tipos_id()
    {
        return $this->hasMany(TipoId::class);
    }

    /**
     * Get the periodos for the campus.
     */
    public function periodos()
    {
        return $this->hasMany(Periodo::class);
    }

    /**
     * Get the programas for the campus.
     */
    public function programas()
    {
        return $this->hasMany(Programa::class);
    }

    /**
     * Get the sedes for the campus.
     */
    public function sedes()
    {
        return $this->hasMany(Sede::class);
    }

    /**
     * Get the jornadas for the campus.
     */
    public function jornadas()
    {
        return $this->hasMany(Jornada::class);
    }
}
