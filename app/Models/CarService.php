<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarService extends BaseModel
{
    use HasFactory;

    protected $fillable = ['garage_id', 'car_id', 'status'];

    /**
     *  function for CarService belongs to Cars
     *
     */
    public function cars()
    {
        return $this->belongsTo(Car::class, 'car_id')->select('id', 'user_id', 'company_name', 'model_name', 'manufacturing_year');
    }

    /**
     *  function for CarService hasMany CarServiceJob
     *
     */
    public function jobs()
    {
        return $this->hasMany(CarServiceJob::class, 'car_service_id')->select('id', 'car_service_id', 'user_id', 'service_type_id', 'status');
    }
}
