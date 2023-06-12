<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public $id = false;

    protected $fillable = [
        'user_id',
        'name',
        'catchPhrase',
        'bs'
    ];
}
