<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\GraphQLException;
use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AuthMutator
{
    protected $authService;
    protected $userService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->userService = new UserService();
    }

    public function signIn($_, array $args)
    {
        return $this->authService->authenticateUser(new Request([
            'email' => $args['email'],
            'password' => $args['password'],
        ]));
    }

    public function signUp($_, array $args)
    {
        $otp = mt_rand(20000, 90000);

        $user = $this->authService->saveUser(new Request([
            'email' => $args['email'],
            'name' => isset($args['name']) ? $args['name'] : null,
            'password' => $args['password'],
            'otp' => $otp,
            'username' => isset($args['username']) ? $args['username'] : null,
        ]));

        // create profile
        $this->userService->createOrUpdateProfile(new Request([
            'user_id' => $user->id,
            'type' => $args['type'],
        ]));

        // send verify email otp

        return $user;
    }

    public function verifyEmailOTP($_, array $args)
    {
        $user = $this->authService->verifyUserOtp(new Request([
            'email' => $args['email'],
            'otp' => $args['otp'],
        ]));

        return $user;
    }

    public function resendVerifyEmail($_, array $args)
    {

        $this->authService->resetUserOtp($args['user_uuid']);

        // resend verify email otp

        return true;
    }

    public function sendResetPasswordEmail($_, array $args)
    {
        $user = User::where('email', $args['email'])->first();

        if ($user) {
            $this->authService->resetUserOtp($user->uuid);
            // send reset password email

            return true;

        } else {
            throw new GraphQLException('User not found');
        }

    }

    public function updatePassword($_, array $args)
    {
        $authUser = Auth::user();

        if ($authUser) {

            $this->authService->updatePassword(new Request([
                'old_password' => isset($args['old_password']) ? $args['old_password'] : null,
                'password' => $args['password'],
            ]));

        } else {

            // verify otp

            $this->authService->verifyUserOtp(new Request([
                'user_uuid' => $args['user_uuid'],
                'otp' => isset($args['otp']) ? $args['otp'] : null,
            ]));

            // update user password

            $this->authService->updatePassword(new Request([
                'user_uuid' => $args['user_uuid'],
                'password' => $args['password'],
            ]));

        }

        return true;

    }

    public function signOut($_, array $args)
    {
        $this->authService->logOut();

        return true;
    }
}
