<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function garages()
    {
        return $this->belongsToMany(Garage::class, 'garage_service_types', 'garage_id', 'service_type_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_service_types', 'user_id', 'service_type_id');
    }
}
