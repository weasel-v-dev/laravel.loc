<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    public $id = false;

    protected $fillable = [
        'user_id',
        'street',
        'suite',
        'city',
        'zipcode',
        'geo_lat',
        'geo_lng'
    ];
}
