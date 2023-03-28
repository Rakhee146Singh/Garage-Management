<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class State extends Model
{
    use HasFactory;

    protected $fillable = ['country_id', 'name'];

    /**
     *  function for State belongsTo Countries
     *
     */
    public function countries()
    {
        return $this->belongsTo(Country::class, 'country_id')->select('id', 'name');
    }

    /**
     *  function for State hasMany Cities
     *
     */
    public function cities()
    {
        return $this->hasMany(City::class, 'state_id')->select('id', 'state_id', 'name');
    }
}
