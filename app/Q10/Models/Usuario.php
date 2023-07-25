<?php

namespace App\Q10\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    /**
     * The table that should be used by the model.
     */
    protected $table = 'usuarios';

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
    ];
    
    /**
     * Get the roles for the usuario.
     */
    public function roles()
    {
        return $this->belongsToMany(Rol::class)->withTimestamps();
    }

    /**
     * Get the perfil for the usuario.
     */
    public function perfil()
    {
        return $this->belongsTo(Perfil::class);
    }
}
