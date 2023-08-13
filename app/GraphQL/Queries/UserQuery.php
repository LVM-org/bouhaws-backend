<?php

namespace App\GraphQL\Queries;

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
        return Profile::orderBy('points', 'desc')->take(10)->get();
    }

    public function userWallet()
    {
        $financialService = new FinancialService();

        return $financialService->getWallet(Auth::user()->id);
    }
}
