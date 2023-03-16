<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GarageServiceType extends BaseModel
{
    use HasFactory;

    protected $fillable = ['garage_id', 'service_type_id'];
}
