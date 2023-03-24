<?php

namespace App\Http\Controllers\V1;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use App\Models\ResetPassword;
use App\Mail\ForgetPasswordMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * API of User login
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $token
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
        auth()->user()->tokens()->delete();
        return ok("Logged out successfully!");
    }

    /**
     * API of User Change Password
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function change_password(Request $request)
    {
        $request->validate([
            'old_password' => 'required|max:8',
            'new_password' => 'required|confirmed|max:8',
        ]);

        //Match The Old Password
        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return error("Old Password Doesn't match!");
        }

        //Old Password and New Password cannot be same
        if ($request->old_password == $request->new_password) {
            return error("Password cannot be same as old password!");
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
    public function send_reset_password_email(Request $request)
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
            return error('Token is Invalid or expired');
        }

        $user = User::where('email', $resetpassword->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        //Delete the token after resetting the password
        ResetPassword::where('email', $user->email)->delete();
        return ok('Password Reset Successfully');
    }
}
