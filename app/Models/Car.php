<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Car extends BaseModel
{
    use HasFactory, Notifiable;

    protected $fillable = ['user_id', 'company_name', 'model_name', 'manufacturing_year'];

    /**
     *  function for Car belongs to Users
     *
     */
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'city_id', 'first_name', 'email', 'address1', 'phone', 'profile_picture');
    }

    /**
     *  function for Car hasMany CarService
     *
     */
    public function carServices()
    {
        return $this->hasMany(CarService::class, 'car_id')->select('id', 'garage_id', 'car_id', 'status');
    }

    /**
     *  function for Car belongsToMany ServiceType
     *
     */
    public function types()
    {
        return $this->belongsToMany(ServiceType::class, 'car_service_types', 'car_id', 'service_type_id')->select('id', 'name');
    }
}
