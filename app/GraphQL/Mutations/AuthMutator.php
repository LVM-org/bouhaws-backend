<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\GraphQLException;
use App\Models\User;
use App\Services\AuthService;
use App\Services\NotificationService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

final class AuthMutator
{
    protected $authService;
    protected $userService;
    protected $notificationService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->userService = new UserService();
        $this->notificationService = new NotificationService();
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
        $otp = mt_rand(2000, 9000);

        // set up user name
        $guessedUsername = explode("@", $args['email']);

        $guessedUsername = $guessedUsername[0];

        $userWithUsername = User::where('username', $guessedUsername)->first();

        if ($userWithUsername) {
            $guessedUsername = $guessedUsername . Str::random(4);
        }

        $user = $this->authService->saveUser(new Request([
            'email' => $args['email'],
            'name' => isset($args['username']) ? $args['username'] : null,
            'password' => $args['password'],
            'otp' => $otp,
            'username' => $guessedUsername,
        ]));

        // create profile
        $this->userService->createOrUpdateProfile(new Request([
            'user_id' => $user->id,
            'type' => $args['type'],
        ]));

        // send verify email otp

        $this->notificationService->sendVerifyEmail((object) [
            'user' => $user,
        ]);

        return $user;
    }

    public function googleAuth($_, array $args)
    {
        $token = $args['token'];
        $access_token = Socialite::driver('google')->getAccessTokenResponse($token);
        $user = Socialite::driver('google')->userFromToken($access_token['access_token']);

        $existingUser = User::where('email', $user->getEmail())->first();

        if ($existingUser) {

            return $this->authService->authenticateUser(new Request([
                'email' => $user->getEmail(),
            ]));

        } else {

            // set up user name
            $guessedUsername = explode("@", $user->getEmail());

            $guessedUsername = $guessedUsername[0];

            $userWithUsername = User::where('username', $guessedUsername)->first();

            if ($userWithUsername) {
                $guessedUsername = $guessedUsername . Str::random(4);
            }

            $user = $this->authService->saveUser(new Request([
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'password' => Str::random(10),
                'otp' => "090900",
                'username' => $guessedUsername,
            ]));

            // create profile
            $this->userService->createOrUpdateProfile(new Request([
                'user_id' => $user->id,
                'type' => $args['type'],
            ]));

            return $this->authService->authenticateUser(new Request([
                'email' => $user->getEmail(),
            ]));
        }

    }

    public function verifyEmailOTP($_, array $args)
    {
        try {
            $user = $this->authService->verifyUserOtp(new Request([
                'email' => $args['email'],
                'otp' => $args['otp'],
            ]));

            return $user;

        } catch (\Throwable $th) {

            throw new GraphQLException($th->getMessage());
        }
    }

    public function resendVerifyEmail($_, array $args)
    {

        $user = $this->authService->resetUserOtp($args['user_uuid']);

        // resend verify email otp

        $this->notificationService->sendVerifyEmail((object) [
            'user' => $user,
        ]);

        return true;
    }

    public function sendResetPasswordEmail($_, array $args)
    {
        $user = User::where('email', $args['email'])->first();

        if ($user) {
            $user = $this->authService->resetUserOtp($user->uuid);
            // send reset password email

            $this->notificationService->sendForgotPasswordEmail((object) [
                'user' => $user,
            ]);

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
