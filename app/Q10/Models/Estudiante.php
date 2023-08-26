<?php

namespace App\Q10\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Estudiante extends Model
{
    use HasFactory;

    /**
     * The table that should be used by the model.
     */
    protected $table = 'estudiantes';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'Fecha_nacimiento' => 'datetime'
    ];

    /**
     * The usuario that belong to the students.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * The tipo_identificacion that belong to the students.
     */
    public function tipo_id()
    {
        return $this->belongsTo(TipoId::class);
    }
    
    /**
     * The cursos that belong to the students.
     */
    public function cursos()
    {
        return $this->belongsToMany(Curso::class)->withTimestamps();
    }

    public function generatePassword()
    {
        return Str::random(8);
    }
}
