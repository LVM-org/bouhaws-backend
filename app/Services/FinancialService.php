<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Wallet;

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
                if ($request->card_id) {

                    // $paymentSuccessful = $this->chargeCard($request);

                    // if (!$paymentSuccessful) {
                    //     abort(500, 'Card Payment Failed');
                    // }

                    // $transaction = $this->addTransactionData($request, $wallet, $walletBalance);

                    // $this->updateUserWallet($request->type, $amount, $wallet, $walletBalance);

                    // return $transaction;
                }

                if ($request->gateway == 'stripe') {

                    // $verificationData = $this->paymentHandler->verifyTransaction($request->reference);

                    // if ($verificationData['status'] == 'success') {
                    //     $amountInFloat = (float) $verificationData['amount'];

                    //     $request->merge(['amount' => $amount / 100]);

                    //     $transaction = $this->addTransactionData($request, $wallet, $walletBalance);

                    //     $this->updateUserWallet($request->type, $request->amount, $wallet, $walletBalance);

                    //     $this->saveCard($verificationData, $wallet, 'paystack');
                    // }
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

}
