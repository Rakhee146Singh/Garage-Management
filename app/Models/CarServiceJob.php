<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarServiceJob extends BaseModel
{
    use HasFactory;

    protected $fillable = ['car_service_id', 'user_id', 'service_type_id', 'start_time', 'end_time', 'status'];

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
        return $this->belongsTo(ServiceType::class, 'service_type_id')->select('id', 'name', 'price');
    }

    /**
     *  function for CarServiceJob hasOne CustomerInvoice
     *
     */
    public function invoice()
    {
        return $this->hasOne(CustomerInvoice::class, 'car_service_job_id')->select('id', 'garage_id', 'car_service_job_id', 'service_num', 'extra_charges', 'total_amount');
    }

    /**
     *  function for CarServiceJob belongsTo User
     *
     */
    public function user()
    {
        return  $this->belongsTo(User::class, 'user_id')->select('id', 'city_id', 'first_name', 'last_name', 'email', 'address1', 'address2', 'phone', 'zipcode', 'profile_picture');
    }
}
