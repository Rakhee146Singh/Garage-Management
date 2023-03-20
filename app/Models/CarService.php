<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarService extends BaseModel
{
    use HasFactory;

    protected $fillable = ['garage_id', 'car_id', 'status'];

    public function cars()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function jobs()
    {
        return $this->hasMany(CarServiceJob::class, 'car_service_id');
    }
}
