<?php

namespace App\GraphQL\Mutations;

use App\Services\FinancialService;

final class FinancialMutator
{
    public function fundWallet($_, array $args)
    {
        $financialService = new FinancialService();

        return $financialService->processSingleCharge((object) [
            "amount" => $args['amount'],
            "paymentMethodId" => $args['paymentMethodId'],
        ]);
    }
}
