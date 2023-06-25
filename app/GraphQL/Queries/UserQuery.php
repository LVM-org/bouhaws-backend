<?php

namespace App\GraphQL\Queries;

use App\Models\Profile;
use App\Services\FinancialService;
use Illuminate\Support\Facades\Auth;

final class UserQuery
{

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
