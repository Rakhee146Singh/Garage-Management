<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceType extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name'];

    public function garages()
    {
        return $this->belongsToMany(Garage::class, 'garage_service_types', 'garage_id', 'service_type_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_service_types');
    }
}
