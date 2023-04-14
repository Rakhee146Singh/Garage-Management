<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'garage_id',
        'invoice_number',
        'tax',
        'total_amount',
    ];

    /**
     *  function for Invoice belongsTo User
     *
     */
    public function user()
    {
        return  $this->belongsTo(User::class, 'user_id')->select('id', 'city_id', 'first_name', 'last_name', 'email', 'address1', 'address2', 'phone', 'profile_picture');
    }

    /**
     *  function for Invoice belongsTo Order
     *
     */
    public function order()
    {
        return  $this->belongsTo(Order::class, 'order_id')->select('id', 'user_id', 'garage_id', 'quantity', 'tax', 'total_amount', 'status',);
    }
}
