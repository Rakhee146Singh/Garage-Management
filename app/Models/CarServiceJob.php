<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarServiceJob extends BaseModel
{
    use HasFactory;

    protected $fillable = ['car_service_id', 'user_id', 'service_type_id', 'status'];

    /**
     * Accessors
     */
    public function getStatusNameAttribute()
    {
        switch ($this->status) {
            case 'P':
                return 'Pending';
            case 'IP':
                return 'In-Progress';
            case 'C':
                return 'Completed';
            default:
                return $this->status;
        }
    }

    /**
     *  function for CarServiceJob belongs to CarService
     *
     */
    public function services()
    {
        return $this->belongsTo(CarService::class, 'car_service_id')->select('id', 'garage_id', 'car_id', 'service_type_id', 'status');
    }

    /**
     *  function for CarServiceJob belongs to ServiceType
     *
     */
    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id')->select('id', 'name');
    }
}
