<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['state_id', 'name'];

    /**
     *  function for City belongs to States
     *
     */
    public function states()
    {
        return $this->belongsTo(State::class, 'state_id');
    }
}
