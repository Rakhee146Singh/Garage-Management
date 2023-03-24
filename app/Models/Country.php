<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

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
