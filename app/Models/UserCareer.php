<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCareer extends Model
{
    use HasFactory;
    protected $table = 'user_career';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $fillable = [
        'id',
        'user_id',
        'career_id',
        'tag'
    ];
}
