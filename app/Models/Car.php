<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends BaseModel
{
    use HasFactory;

    protected $fillable = ['user_id', 'company_name', 'model_name', 'manufacturing_year'];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function carServices()
    {
        return $this->hasMany(CarService::class);
    }
}
