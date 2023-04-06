<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'city_id',
        'garage_id',
        'service_type_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'type',
        'billable_name',
        'address1',
        'address2',
        'zipcode',
        'phone',
        'profile_picture',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     *  function for User belongToMany ServiceType
     *
     */
    public function service()
    {
        return $this->belongsToMany(ServiceType::class, 'user_service_types', 'user_id', 'service_type_id')->select('id', 'name');
    }

    /**
     *  function for User hasMany Cars
     *
     */
    public function cars()
    {
        return $this->hasMany(Car::class, 'user_id')->select('id', 'user_id', 'company_name', 'model_name', 'manufacturing_year');
    }

    /**
     *  function for User belongToMany Garages
     *
     */
    public function garages()
    {
        return $this->belongsToMany(Garage::class, 'garage_users', 'user_id', 'garage_id');
    }

    /**
     *  function for User hasOne CarServiceJob
     *
     */
    public function job()
    {
        return $this->hasOne(CarServiceJob::class, 'user_id')->select('id', 'car_service_id', 'user_id', 'service_type_id', 'status');
    }

    /**
     *  function for created_by and updated_by data for users
     *
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_by = auth()->user() ? auth()->user()->id : User::where('type', 'admin')->first()->id ?? null;
        });
        static::updating(function ($model) {
            $model->updated_by = auth()->user() ? auth()->user()->id : User::where('type', 'admin')->first()->id;
        });
    }
}
