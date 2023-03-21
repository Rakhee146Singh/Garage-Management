<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     *  function for ServiceType belongsToMany Garages
     *
     */
    public function garages()
    {
        return $this->belongsToMany(Garage::class, 'garage_service_types', 'garage_id', 'service_type_id');
    }

    /**
     *  function for ServiceType belongsToMany Users
     *
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_service_types', 'user_id', 'service_type_id');
    }
}
