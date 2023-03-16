<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * API of listing User data.
     *
     * @return $users
     */
    public function list(Request $request)
    {
        $request->validate([
            'search'        => 'nullable|string',
            'sortOrder'     => 'nullable|in:asc,desc',
            'sortField'     => 'nullable|string',
            'perPage'       => 'nullable|integer',
            'currentPage'   => 'nullable|integer'
        ]);
        $query = User::query(); //query

        /* Searching */
        if (isset($request->search)) {
            $query = $query->where("first_name", "LIKE", "%{$request->search}%");
        }
        /* Sorting */
        if ($request->sortField || $request->sortOrder) {
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
        $users   = $query->get();
        $data       = [
            'count' => $count,
            'data'  => $users
        ];
        return ok('User list', $data);
    }

    /**
     * API of new create User.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response $user
     */
    public function create(Request $request)
    {
        $request->validate([
            'city_id'           => 'required',
            'first_name'        => 'required',
            'last_name'         => 'required',
            'email'             => 'required|email',
            'password'          => 'required',
            'type'              => 'nullable|in:mechanic,customer',
            'billable_name'     => 'nullable',
            'address1'          => 'required',
            'address2'          => 'required',
            'zipcode'           => 'required|integer|min:6',
            'phone'             => 'required',
            'profile_picture'   => 'nullable'
        ]);
        $request['password'] = Hash::make($request->password);
        $user = User::create($request->only('city_id', 'first_name', 'last_name', 'email', 'password', 'type', 'billable_name', 'address1', 'address2', 'zipcode', 'phone', 'profile_picture'));
        //    //enter data in pivot table
        //    $user->garages()->attach($request->garages);
        return ok('User created successfully!', $user);
    }

    /**
     * API to get User with $id.
     *
     * @param  \App\User  $id
     * @return \Illuminate\Http\Response $user
     */
    public function show($id)
    {
        $country = User::findOrFail($id);
        return ok('Country retrieved successfully', $country);
    }

    /**
     * API of Update User Data.
     *
     * @param  \App\User  $id
     * @return \Illuminate\Http\Response $user
     */
    public function update(Request $request, $id)
    {
        $country = User::findOrFail($id);
        $request->validate([
            'name'         => 'required',
        ]);
        $country->update($request->only('name'));
        return ok('Country updated successfully!', $country);
    }

    /**
     * API of Delete User data.
     *
     * @param  \App\User  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        User::findOrFail($id)->delete();
        return ok('Country deleted successfully');
    }

    /**
     * API of User login
     *
     * @param  \Illuminate\Http\Request  $request
     * @return $token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return error("User with this email is not found!");
        }
        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken($request->email)->plainTextToken;

            $data = [
                'token' => $token,
                'user'  => $user
            ];
            return ok('User Logged in Succesfully', $data);
        } else {
            return error("Password is incorrect");
        }
    }

    /**
     * API of User Logout
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return ok("Logged out successfully!");
    }
}
