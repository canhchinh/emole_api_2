<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sns extends Model
{
    use HasFactory;
    protected $table = 'sns';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $incrementing = true;
    protected $fillable = [
        'user_id',
        'twitter',
        'instagram',
        'youtube',
        'tiktok',
        'facebook',
        'line',
        'pinterest',
        'created_at',
        'updated_at',
    ];
}
