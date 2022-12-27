<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * Get the course that owns the evaluation.
     */
    public function courses()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the subjects that owns the evaluation.
     */
    public function subjects()
    {
        return $this->belongsTo(Subject::class);
    }
}
