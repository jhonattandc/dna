<?php

namespace App\Q10\Models;


use Illuminate\Database\Eloquent\Relations\Pivot;


class JornadaSede extends Pivot {
    
        /**
        * The table that should be used by the model.
        */
        protected $table = 'jornada_sede';
    
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
            'jornada_id' => 'integer',
            'sede_id' => 'integer',
        ];
    
        /**
        * Get the jornada that owns the jornada_sede.
        */
        public function jornada()
        {
            return $this->belongsTo(Jornada::class);
        }
    
        /**
        * Get the sede that owns the jornada_sede.
        */
        public function sede()
        {
            return $this->belongsTo(Sede::class);
        }
    
        /**
        * Get the cursos for the jornada_sede.
        */
        public function cursos()
        {
            return $this->hasMany(Curso::class);
        }
}