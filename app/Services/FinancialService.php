<?php

namespace App\Services;

use App\Exceptions\GraphQLException;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;

class FinancialService
{
    public function getWallet($user_id)
    {
        $wallet = Wallet::where('user_id', $user_id)->first();

        if ($wallet == null) {
            $wallet = Wallet::create([
                'user_id' => $user_id,
                'total_balance' => 0,
                'credited_amount' => 0,
                'debited_amount' => 0,
            ]);

            $wallet->save();

            $wallet = Wallet::where('user_id', $user_id)->first();
        }

        return $wallet;
    }

    public function createTransaction($request)
    {
        $transaction = Transaction::where('reference', $request->reference)->first();

        if ($transaction == null) {
            $wallet = Wallet::where('user_id', $request->user_id)->first();

            $walletBalance = $wallet->total_balance;

            $amount = $request->amount;

            if (!$request->auto_generated) {

                if ($request->gateway == 'stripe') {

                    $transaction = $this->addTransactionData($request, $wallet, $walletBalance);

                    $this->updateUserWallet($request->type, $request->amount, $wallet, $walletBalance);

                }

            } else {
                $transaction = $this->addTransactionData($request, $wallet, $walletBalance);
                $this->updateUserWallet($request->type, $amount, $wallet, $walletBalance);
            }
        } else {
            $transaction->update([
                'status' => $request->status,
                'amount' => $request->amount,
            ]);
        }

        return $transaction;
    }

    private function updateUserWallet($type, $amount, $wallet, $walletBalance)
    {
        if ($type == 'debit') {
            $wallet->update([
                'debited_amount' => $wallet->debited_amount + $amount,
                'total_balance' => $walletBalance - $amount,
            ]);
        }

        if ($type == 'credit') {
            $wallet->update([
                'credited_amount' => $wallet->debited_amount + $amount,
                'total_balance' => $walletBalance + $amount,
            ]);
        }
    }

    private function addTransactionData($request, $wallet, $walletBalance)
    {
        $currency = 'usd';

        $transaction = Transaction::create([
            'user_id' => $request->user_id,
            'wallet_id' => $wallet->id,
            'amount' => $request->amount,
            'currency' => $currency,
            'wallet_balance' => $walletBalance,
            'charge_id' => $request->type == 'credit' ? $wallet->id : $request->charge_id,
            'chargeable_type' => $request->type == 'credit' ? 'wallet' : $request->chargeable_type,
            'description' => $request->description,
            'status' => $request->status,
            'dr_or_cr' => $request->type,
            'charges' => $request->charges,
            'reference' => $request->reference,
        ]);

        $transaction->save();

        return $transaction;
    }

    public function processSingleCharge($request)
    {
        $authUser = Auth::user();

        try {
            $stripeCharge = $authUser->charge(
                $request->amount, $request->paymentMethodId, [
                    "user_uuid" => $authUser->uuid,
                ]
            );

            // at this point the charge was successful
            $this->createTransaction((object) [
                "amount" => (float) $request->amount / 100,
                "reference" => $stripeCharge['id'],
                "type" => 'credit',
                "user_id" => $authUser->id,
                "auto_generated" => false,
                "status" => 'successful',
                "gateway" => "stripe",
                "description" => "Wallet funding",
                "charges" => 0,
            ]);

            return true;

        } catch (\Throwable $th) {

            throw new GraphQLException($th->getMessage());

        }
    }

}
