<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    public $timestamps = false;
    public function envialoAccounts()
    {
        return $this->hasMany('\App\Models\EnvialoAccount');
    }
    
}
