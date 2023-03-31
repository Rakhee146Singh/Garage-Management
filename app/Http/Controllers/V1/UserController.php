<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * API of listing User data.
     *
     * @return json $users
     */
    public function list(Request $request)
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
        $query = User::query(); //query

        if (auth()->user()->type == 'owner') {
            $query = User::query()->with('garages.services.cars.users');
        }
        /* Searching */
        if (isset($request->search)) {
            $query = $query->where("billable_name", "LIKE", "%{$request->search}%");
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
        $data           = [
            'count'     => $count,
            'users'     => $query->get()
        ];
        return ok('User list', $data);
    }

    /**
     * API for Create Mechanic and Customer Data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $user
     */
    public function create(Request $request)
    {
        $request->validate(
            [
                'city_id'             => 'required|exists:cities,id',
                'garage_id'           => 'required_if:type,mechanic,customer|exists:garages,id',
                'service_type_id.*'   => 'required_if:type,mechanic,customer|exists:service_types,id',
                'first_name'          => 'required|alpha|max:30',
                'last_name'           => 'required|alpha|max:30',
                'email'               => 'required|email|unique:users',
                'password'            => 'required|string|max:8',
                'type'                => 'required|in:mechanic,customer',
                'billable_name'       => 'nullable|string',
                'address1'            => 'required|string|max:100',
                'address2'            => 'required|string|max:100',
                'zipcode'             => 'required|integer|min:6',
                'phone'               => 'required|unique:users|integer|min:10',
                'profile_picture'     => 'required|mimes:jpg,jpeg,png,pdf',
            ]
        );
        $request['password'] = Hash::make($request->password);
        $imageName = str_replace(".", "", (string)microtime(true)) . '.' . $request->profile_picture->getClientOriginalExtension();
        $request->profile_picture->storeAs("public/profiles", $imageName);

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
                    'billable_name' => $billable_name
                ] +
                [
                    'profile_picture' => $imageName
                ]
        );

        /** Insert data in pivot table for customer and mechanic */
        if ($user->type != 'owner') {
            $user->service()->syncWithoutDetaching($request->service_type_id);
            $user->garages()->attach([$request->garage_id => ['is_owner' => false]]);
        }
        return ok('User registered successfully!', $user);
    }


    /**
     * API to get User with $id.
     *
     * @param  \App\User  $id
     * @return json $user
     */
    public function show($id)
    {
        $user = User::with('garages', 'service', 'cars')->findOrFail($id);
        return ok('User retrieved successfully', $user);
    }

    /**
     * API of Update User Data.
     *
     * @param  \App\User  $id
     * @return json $user
     */
    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'city_id'              => 'required|exists:cities,id',
                'first_name'           => 'required|alpha|max:30',
                'last_name'            => 'required|alpha|max:30',
                'email'                => 'required|email',
                'password'             => 'required|string|max:8',
                'type'                 => 'required|in:mechanic,customer',
                'billable_name'        => 'nullable|string|max:20',
                'address1'             => 'required|string|max:100',
                'address2'             => 'required|string|max:100',
                'zipcode'              => 'required|integer|min:6',
                'phone'                => 'required|integer|min:10',
                'profile_picture'      => 'required|mimes:jpg,jpeg,png,pdf',
                'garage_id'            => 'required_if:type,mechanic,customer|exists:garages,id',
                'service_type_id.*'    => 'required_if:type,mechanic,customer|exists:service_types,id',
            ]
        );
        $request['password'] = Hash::make($request->password);
        $billable_name = $request->first_name . " " . $request->last_name;

        $user = User::findOrFail($id);

        if ($user->profile_picture) {
            Storage::delete("public/profiles/" . $user->profile_picture);
            $imageName = str_replace(".", "", (string)microtime(true)) . '.' . $request->profile_picture->getClientOriginalExtension();
            $request->profile_picture->storeAs("public/profiles", $imageName);
        }

        $user->update(
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
                    'billable_name' => $billable_name
                ] +
                [
                    'profile_picture' => $imageName
                ]
        );
        $user->service()->syncWithoutDetaching($request->service_type_id);
        $user->garages()->sync([$request->garage_id => ['is_owner' => false]]);
        return ok('User Updated successfully!', $user);
    }

    /**
     * API of Delete User data.
     *
     * @param  \App\User  $id
     * @return json
     */
    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->cars()->delete();
        $user->service()->delete();
        $user->delete();
        return ok('User deleted successfully');
    }
}
