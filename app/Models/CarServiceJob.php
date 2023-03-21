<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarServiceJob extends BaseModel
{
    use HasFactory;

    protected $fillable = ['car_service_id', 'user_id', 'service_type_id', 'status'];

    /**
     *  function for CarServiceJob belongs to CarService
     *
     */
    public function services()
    {
        return $this->belongsTo(CarService::class, 'car_service_id');
    }
}
