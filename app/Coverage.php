<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coverage extends Model
{
    //protected $connection = 'products_server';

    public function premiums(){
        return $this->hasMany('App\Premium');
    }

}
