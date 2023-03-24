<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Garage extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'state_id',
        'country_id',
        'user_id',
        'name',
        'address1',
        'address2',
        'zipcode',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     *  function for Garage belongsToMany ServiceType
     *
     */
    public function services()
    {
        return $this->belongsToMany(ServiceType::class, 'garage_service_types', 'garage_id', 'service_type_id')->select('id', 'name');
    }

    /**
     *  function for Garage belongsToMany Users
     *
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'garage_users', 'garage_id', 'user_id')->withPivot('is_owner')->select('id', 'city_id', 'first_name', 'email', 'address1', 'phone', 'profile_picture');
    }
}
