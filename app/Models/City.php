<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public $timestamps = false;
    protected $fillable = ['state_id', 'name'];

    /**
     *  function for City belongs to States
     *
     */
    public function states()
    {
        return $this->belongsTo(State::class, 'state_id')->select('id', 'country_id', 'name');
    }
}
