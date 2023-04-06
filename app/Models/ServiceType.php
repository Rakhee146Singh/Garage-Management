<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];

    /**
     *  function for ServiceType belongsToMany Garages
     *
     */
    public function garages()
    {
        return $this->belongsToMany(Garage::class, 'garage_service_types', 'garage_id', 'service_type_id')->select('id', 'city_id', 'state_id', 'country_id', 'user_id', 'name', 'address1', 'address2');
    }

    /**
     *  function for ServiceType belongsToMany Users
     *
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_service_types', 'user_id', 'service_type_id')->select('id', 'city_id', 'first_name', 'email', 'address1', 'phone', 'profile_picture');
    }
}
