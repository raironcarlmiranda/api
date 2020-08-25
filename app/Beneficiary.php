<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Beneficiary extends Model
{
    //protected $connection = 'products_server';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'birth_place',
        'gender',
        'relation',
        'beneficiaryable_id',
        'beneficiaryable_type',
    ];

    public function beneficiaryable()
    {
        return $this->morphTo();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
        
            $model->birthdate = ($model->birthdate ? date("Y-m-d", strtotime($model->birthdate)):null) ;
        });

        static::updating(function($model)
        {
            
            $model->birthdate = ($model->birthdate ? date("Y-m-d", strtotime($model->birthdate)):null) ;
        });
    }
}
