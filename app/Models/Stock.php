<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stock extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'garage_id',
        'name',
        'description',
        'price',
        'quantity',
        'is_available',
        'manufacture_date',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     *  function for Stock belongsTo Garage
     *
     */
    public function garage()
    {
        return  $this->belongsTo(Garage::class, 'garage_id')->select('id', 'city_id', 'state_id', 'country_id', 'user_id', 'name', 'address1', 'address2');
    }

    /**
     *  function for Order hasMany Stocks
     *
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_stocks', 'stock_id', 'order_id')->select('id', 'user_id', 'garage_id', 'quantity', 'tax', 'total_amount');
    }
}
