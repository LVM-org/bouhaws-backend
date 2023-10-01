<?php

namespace App\GraphQL\Queries;

use App\Models\Notification;
use App\Models\Profile;
use App\Models\User;
use App\Services\FinancialService;
use Illuminate\Support\Facades\Auth;

final class UserQuery
{
    public function authUser($_, array $args)
    {
        if (Auth::user()) {

            // set wallet if it doesn't exist
            $financialService = new FinancialService();

            $financialService->getWallet(Auth::user()->id);

            return User::where('id', Auth::user()->id)->first();

        }

        return null;

    }

    public function leaderboard($_, array $args)
    {
        return Profile::where('type', 'student')->get()->sortByDesc('total_point')->take(10)->toArray();
    }

    public function userWallet($_, array $args)
    {
        $financialService = new FinancialService();

        return $financialService->getWallet(Auth::user()->id);
    }

    public function myNotifications($_, array $args)
    {
        return Notification::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->take(50)->get();
    }

}
