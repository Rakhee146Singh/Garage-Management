<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $timestamps = false;

    protected $fillable = ['name'];

    /**
     *  function for Country hasMany States
     *
     */
    public function states()
    {
        return $this->hasMany(State::class, 'country_id')->select('id', 'country_id', 'name');
    }
}
