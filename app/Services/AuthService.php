<?php

namespace App\Services;

use App\Exceptions\GraphQLException;
use App\Models\User;
use App\Models\UserAuthTokens;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function saveUser($request)
    {
        $user = User::where('email', $request->email)->where('email', '!=', null)->first();

        if ($user == null) {

            if (isset($request->username)) {
                $user = User::where('username', $request->username)->first();

                if ($user) {
                    throw new GraphQLException('User with username exist');
                }
            }

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : null,
                'otp' => $request->otp,
                'otp_expires_at' => Carbon::now()->addMinutes(43200),
            ]);

            $user->save();

        } else {

            if ($user->email_verified_at) {
                throw new GraphQLException('User with email already exist');
            } else {
                return $user;
            }

        }

        return $user;
    }

    public function authenticateUser($request)
    {
        if ($request->has('password')) {

            $username = $request->email;

            $user = User::where('email', $username)->first();

            if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $token = Auth::attempt([
                    'email' => $username,
                    'password' => $request->password,
                ]);
            } else {
                $token = Auth::attempt([
                    'username' => $username,
                    'password' => $request->password,
                ]);

                $user = User::where('username', $username)->first();
            }

        } else {
            throw new GraphQLException('Please enter a password');
        }

        if (env('APP_STATE') == 'prod') {
            if ($token) {
                // invalidate existing auth token
                $existingAuthTokens = UserAuthTokens::where('auth_id', $user->uuid)->get();

                foreach ($existingAuthTokens as $authToken) {
                    try {
                        Auth::setToken($authToken->auth_token)->invalidate();
                        $authToken->delete();
                    } catch (\Throwable $th) {
                        //throw $th;
                        $authToken->delete();
                        continue;
                    }

                }
                // save new token
                UserAuthTokens::create([
                    "auth_id" => $user->uuid,
                    "auth_token" => $token,
                ])->save();
            }
        }

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function resetUserOtp($userUuid)
    {
        $otp = mt_rand(2000, 9000);
        $user = User::where('uuid', $userUuid)->first();

        if ($user == null) {
            $user = User::where('email', $userUuid)->first();
        }

        if ($user) {
            $user->otp = $otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(60);
            $user->save();

            return $user;

        } else {
            throw new GraphQLException('User not found');
        }
    }

    public function updatePassword($request)
    {
        $user = Auth::user();

        if ($user == null) {
            $user = User::where('email', $request->user_uuid)->first();

            if ($user == null) {
                $user = User::where('uuid', $request->user_uuid)->first();
            }
        } else {
            if (Hash::check($request->old_password, $user->password)) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
                return 'password updated';
            } else {
                throw new GraphQLException('Old password is wrong');
            }
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return "password updated";
    }

    public function updateUser($request)
    {
        $user = User::where('uuid', $request->user_uuid)->first();

        if ($user) {
            $user->update([
                'name' => $request->name ? $request->name : $user->name,
            ]);

            return $user;
        } else {
            throw new GraphQLException('User does not exist');
        }
    }

    public function verifyUserOtp($request)
    {
        $user = User::where('uuid', $request->user_uuid)->first();

        if ($user == null) {
            $user = User::where('email', $request->email)->first();
        }

        if ($user->email_verified_at != null) {
            return "Otp Verified";
        } elseif ($user->otp_expires_at && $user->otp_expires_at < Carbon::now()) {
            abort(403, "Otp expired");
        }
        if ($user->otp == trim($request->otp)) {
            $user->update([
                'email_verified_at' => Carbon::now(),
            ]);
        } else {
            throw new GraphQLException("Incorrect OTP! Enter valid otp");
        }

        return 'Otp Verified';
    }

    public function updateUserStatus($request)
    {
        $user = User::where('uuid', $request->user_uuid)->first();

        if ($user) {
            $user->update([
                "status" => $request->status,
            ]);
        } else {
            throw new GraphQLException("User not found");
        }

        return "User status updated";
    }

    public function logOut()
    {
        auth()->logout();
    }
}
