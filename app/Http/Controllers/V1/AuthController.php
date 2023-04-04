<?php

namespace App\Http\Controllers\V1;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Stock;
use App\Models\Garage;
use App\Mail\ServiceMail;
use App\Models\CarService;
use App\Models\GarageUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ResetPassword;
use App\Mail\ForgetPasswordMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * API for User(Owner,Mechanic,Customer) Registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $user
     */
    public function register(Request $request)
    {
        $request->validate(
            [
                'city_id'                    => 'required|exists:cities,id',
                'garage_id'                  => 'required_if:type,mechanic,customer|exists:garages,id',
                'service_type_id.*'          => 'required_if:type,mechanic,customer|exists:service_types,id',
                'first_name'                 => 'required|alpha|max:30',
                'last_name'                  => 'required|alpha|max:30',
                'email'                      => 'required|email|unique:users',
                'password'                   => 'required|string|max:8',
                'type'                       => 'required|in:mechanic,customer,owner',
                'billable_name'              => 'nullable|string',
                'address1'                   => 'required|string|max:100',
                'address2'                   => 'required|string|max:100',
                'zipcode'                    => 'required|integer|min:6',
                'phone'                      => 'required|unique:users|integer|min:10',
                'profile_picture'            => 'required|mimes:jpg,jpeg,png,pdf',
                'company_name'               => 'required_if:type,customer|alpha|max:20',
                'model_name'                 => 'required_if:type,customer|string|max:20',
                'manufacturing_year'         => 'required_if:type,customer|date_format:Y',
            ]
        );
        $request['password'] = Hash::make($request->password);

        if ($request->hasFile('profile_picture')) {
            $imageName = str_replace(".", "", (string)microtime(true)) . '.' . $request->profile_picture->getClientOriginalExtension();
            $request->profile_picture->storeAs("public/profiles", $imageName);
        }

        $billable_name = $request->first_name . " " . $request->last_name;
        $user = User::create(
            $request->only(
                'city_id',
                'first_name',
                'last_name',
                'email',
                'password',
                'type',
                'address1',
                'address2',
                'zipcode',
                'phone'
            ) +
                [
                    'billable_name'     => $billable_name
                ] +
                [
                    'profile_picture'   => $imageName
                ]
        );

        /** Insert data in pivot table for customer and mechanic */
        if ($user->type != 'owner') {
            $user->service()->syncWithoutDetaching($request->service_type_id);
            $user->garages()->attach([$request->garage_id => ['is_owner' => false]]);
        }

        /** Insert car and service type detail when Customer register with pivot table insertion */
        if ($user->type == 'customer') {
            $car = $user->cars()->create(
                $request->only(
                    'company_name',
                    'model_name',
                    'manufacturing_year'
                )
            );
            $car->types()->attach($request->service_type_id);

            /** Insertion in Car Service Table with Car Details */
            $services = [];
            foreach ($request->service_type_id as $service_id) {
                $service = CarService::create(
                    [
                        'garage_id'         => $request->garage_id,
                        'car_id'            => $car->id
                    ],
                    [
                        'service_type_id'   => $service_id
                    ]
                );
                array_push($services, $service);
            }

            /** Sending mail to Garage owner with Customer Car details and Car Service Id*/
            $owner_data = GarageUser::where('garage_id', $request->garage_id)->where('is_owner', true)->first();
            $owner      = User::findOrFail($owner_data->user_id);
            Mail::to($owner->email)->send(new ServiceMail($owner, $user, $car, $services));
        }
        return ok('User registered successfully!', $user);
    }

    /**
     * API of User login
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'         => 'required|email|exists:users',
            'password'      => 'required|string|max:8',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return error("User with this email is not found!", [], 'notfound');
        }
        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken($request->email)->plainTextToken;

            $data       = [
                'token' => $token,
                'user'  => $user
            ];
            return ok('User Logged in Succesfully', $data);
        } else {
            return error("Password is incorrect", [], 'validation');
        }
    }

    /**
     * API of User Logout
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return ok("Logged out successfully!");
    }

    /**
     * API of User Change Password
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password'      => 'required|max:8',
            'new_password'      => 'required|confirmed|max:8',
        ]);

        //Match The Old Password
        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return error("Old Password Doesn't match!", [], 'forbidden');
        }

        //Old Password and New Password cannot be same
        if ($request->old_password == $request->new_password) {
            return error("Password cannot be same as old password!", [], 'validation');
        }

        //Update the new Password
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        return ok("Password changed successfully!");
    }

    /**
     * API of Send User Reset Password email
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function resetMail(Request $request)
    {
        $request->validate([
            'email'         => 'required|email',
        ]);
        //Check user's mail exists or not
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return error('Email does not exists');
        }

        //generate token
        $token = Str::random(40);
        ResetPassword::create([
            'email'         => $user->email,
            'token'         => $token,
            'created_at'    => Carbon::now()
        ]);
        //Sending Email with Password Reset View
        Mail::to($user->email)->send(new ForgetPasswordMail($token));
        return ok('Reset Password Email Successfully');
    }

    /**
     * API of User Reset Password
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $token
     */
    public function reset(Request $request, $token)
    {
        //Delete Token older than 1 minute
        $formatted = Carbon::now()->subMinutes(1)->toDateTimeString();
        ResetPassword::where('created_at', $formatted)->delete();

        $request->validate([
            'password'      => 'required|confirmed|max:8',
        ]);
        $resetpassword = ResetPassword::where('token', $token)->first();
        if (!$resetpassword) {
            return error('Token is Invalid or expired', [], 'unauthenticated');
        }

        $user = User::where('email', $resetpassword->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        //Delete the token after resetting the password
        ResetPassword::where('email', $user->email)->delete();
        return ok('Password Reset Successfully');
    }

    /**
     * API of listing Garage data.
     *
     * @return json $garages
     */
    public function garageList(Request $request)
    {
        $request->validate(
            [
                'search'             => 'nullable|string',
                'sortOrder'          => 'nullable|in:asc,desc',
                'sortField'          => 'nullable|string',
                'perPage'            => 'nullable|integer',
                'currentPage'        => 'nullable|integer',
                'city_id'            => 'nullable|exists:cities,id',
                'state_id'           => 'nullable|exists:states,id',
                'country_id'         => 'nullable|exists:countries,id',
            ]
        );
        $query = Garage::query()->with('services'); //query

        /* Filters */
        if ($request->city_id) {
            $query->whereHas('cities', function ($query) use ($request) {
                $query->where('id', $request->city_id);
            });
        }
        if ($request->state_id) {
            $query->whereHas('cities.states', function ($query) use ($request) {
                $query->where('id', $request->state_id);
            });
        }
        if ($request->country_id) {
            $query->whereHas('cities.states.countries', function ($query) use ($request) {
                $query->where('id', $request->country_id);
            });
        }

        /* Searching */
        if (isset($request->search)) {
            $query = $query->where("name", "LIKE", "%{$request->search}%");
        }

        /* Sorting */
        if ($request->sortField && $request->sortOrder) {
            $query = $query->orderBy($request->sortField, $request->sortOrder);
        }

        /* Pagination */
        $count = $query->count();
        if ($request->perPage && $request->currentPage) {
            $perPage        = $request->perPage;
            $currentPage    = $request->currentPage;
            $query          = $query->skip($perPage * ($currentPage - 1))->take($perPage);
        }
        /* Get records */
        $data           = [
            'count'     => $count,
            'garages'   => $query->get()
        ];
        return ok('Garage list', $data);
    }

    /**
     * API of listing Stock data.
     *
     * @return json $stocks
     */
    public function stockList(Request $request)
    {
        $request->validate(
            [
                'search'        => 'nullable|string',
                'sortOrder'     => 'nullable|in:asc,desc',
                'sortField'     => 'nullable|string',
                'perPage'       => 'nullable|integer',
                'currentPage'   => 'nullable|integer'
            ]
        );
        $query = Stock::query()->with('garage'); //query

        /* Searching */
        if (isset($request->search)) {
            $query = $query->where("name", "LIKE", "%{$request->search}%");
        }
        /* Sorting */
        if ($request->sortField && $request->sortOrder) {
            $query = $query->orderBy($request->sortField, $request->sortOrder);
        }

        /* Pagination */
        $count = $query->count();
        if ($request->perPage && $request->currentPage) {
            $perPage        = $request->perPage;
            $currentPage    = $request->currentPage;
            $query          = $query->skip($perPage * ($currentPage - 1))->take($perPage);
        }
        /* Get records */
        $data           = [
            'count'     => $count,
            'stocks'    => $query->get()
        ];
        return ok('Stocks list', $data);
    }
}
