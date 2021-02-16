<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerChildren extends Model
{
    use HasFactory;
    protected $table = 'career_children';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $incrementing = true;
    protected $fillable = [
        'id',
        'title',
        'career_id',
        'created_at',
        'updated_at',
    ];
}
