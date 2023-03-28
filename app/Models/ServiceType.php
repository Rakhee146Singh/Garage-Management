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

    /**
     *  function for ServiceType belongsToMany Users
     *
     */
    public function cars()
    {
        return $this->belongsToMany(Car::class, 'car_service_types', 'car_id', 'service_type_id')->select('id', 'user_id', 'company_name', 'model_name', 'manufacturing_year');
    }
}
