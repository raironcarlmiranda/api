<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    //protected $connection = 'products_server';

    protected $fillable = [
        'name',
        'breed',
        'gender',
        'birthdate',
        'age',
        'origin',
        'petable_id',
        'petable_type',
        //'document',
    ];

    public function petable()
    {
        return $this->morphTo();
    }
}
