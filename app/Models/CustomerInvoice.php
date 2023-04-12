<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerInvoice extends Model
{
    use HasFactory;

    protected $fillable = ['garage_id', 'car_service_job_id', 'service_num', 'extra_charges', 'total_amount'];

    /**
     *  function for CustomerInvoice belongsTo User
     *
     */
    public function user()
    {
        return  $this->belongsTo(User::class, 'user_id')->select('id', 'city_id', 'first_name', 'last_name', 'email', 'address1', 'address2', 'phone', 'profile_picture');
    }

    /**
     *  function for Invoice belongsTo CarServiceJob
     *
     */
    public function serviceJob()
    {
        return  $this->belongsTo(CarServiceJob::class, 'car_service_job_id')->select('id', 'car_service_id', 'user_id', 'service_type_id', 'start_time', 'end_time', 'status',);
    }
}
