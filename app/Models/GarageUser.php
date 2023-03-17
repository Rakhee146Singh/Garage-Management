<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GarageUser extends BaseModel
{
    use HasFactory;

    protected $fillable = ['garage_id', 'user_id', 'is_owner'];
}
