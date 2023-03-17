<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserServiceType extends BaseModel
{
    use HasFactory;

    protected $table = 'user_service_types';

    protected $fillable = ['user_id', 'service_type_id'];
}
