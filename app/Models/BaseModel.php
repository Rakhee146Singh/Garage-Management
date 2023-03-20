<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;

    /**
     *  function for created_by and updated_by data for users
     *
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_by = auth()->user() ? auth()->user()->id : User::where('type', 'admin', 'owner', 'mechanic')->first()->id;
        });
        static::updating(function ($model) {
            $model->updated_by = auth()->user() ? auth()->user()->id : User::where('type', 'admin', 'owner', 'mechanic')->first()->id;
        });
        static::deleting(function ($model) {
            $model->updated_by = auth()->user() ? auth()->user()->id : User::where('type', 'admin', 'owner', 'mechanic')->first()->id;
        });
    }
}
