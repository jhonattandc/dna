<?php

namespace App\Prosegur\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alarm extends Model
{
    use HasFactory;

    /**
     * The table that should be used by the model.
     */
    protected $table = 'prosegur_alarms';

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
     * The attributes that should be mutated to dates.
     *
     * @var array<string>
     */
    protected $dates = [
        'triggered_at',
    ];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array<string>
     */
    protected $visible = [
        'id',
        'system',
        'location',
        'event',
        'operator',
        'triggered_at',
        'created_at',
        'updated_at',
    ];
}
