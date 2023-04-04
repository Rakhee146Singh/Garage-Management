<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'garage_id',
        'stock_id',
        'quantity',
        'tax',
        'total_amount',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * Accessors
     */
    public function getStatusNameAttribute()
    {
        switch ($this->status) {
            case 'P':
                return 'Pending';
            case 'A':
                return 'Accept';
            case 'R':
                return 'Reject';
            default:
                return $this->status;
        }
    }

    /**
     *  function for Order belongsTo User
     *
     */
    public function user()
    {
        return  $this->belongsTo(User::class, 'user_id')->select('id', 'city_id', 'first_name', 'last_name', 'email', 'address1', 'address2', 'phone', 'profile_picture');
    }

    /**
     *  function for Order hasMany Stocks
     *
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id')->select('id', 'garage_id', 'name', 'description', 'price', 'manufacture_date');
    }

    /**
     *  function for Order hasOne Invoice
     *
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'order_id')->select('id', 'order_id', 'stock_id', 'user_id', 'garage_id', 'invoice_number', 'quantity', 'tax', 'total_amount');
    }
}
