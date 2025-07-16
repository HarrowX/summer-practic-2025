<?php

namespace App\Services;

use App\Models\Promo;
use App\Models\User;
use App\Models\HistoryPromo;
use Bavix\LaravelWallet\Models\Transaction;
use Bavix\LaravelWallet\Models\Wallet;
use Illuminate\Support\Facades\DB;

class PromoService
{
    public function applyPromo(User $user, string $code): array
    {
        return DB::transaction(function () use ($user, $code) {
            $promo = Promo::where('code', $code)->first();

            if (!$promo) {
                return $this->errorResponse('Промокод не найден');
            }

            if (!$promo->isAvailable()) {
                return $this->handleUnavailablePromo($promo);
            }

            if ($this->alreadyUsed($user, $promo)) {
                return $this->errorResponse('Вы уже использовали этот промокод');
            }

            return $this->processPromo($user, $promo);
        });
    }

    private function alreadyUsed(User $user, Promo $promo): bool
    {
        return HistoryPromo::where('user_id', $user->id)
            ->where('promo_id', $promo->id)
            ->exists();
    }

    private function processPromo(User $user, Promo $promo): array
    {
        try {
            /** @var Wallet $wallet */
            $wallet = $user->getWallet('default') ??
                $user->createWallet(['name' => 'Default Wallet']);

            /** @var Transaction $transaction */
            $transaction = $wallet->deposit(
                $promo->count_points,
                [
                    'description' => 'Пополнение по промокоду ' . $promo->code,
                    'meta' => [
                        'promo_id' => $promo->id,
                        'promo_code' => $promo->code
                    ]
                ],
                true // confirmed
            );

            HistoryPromo::create([
                'user_id' => $user->id,
                'promo_id' => $promo->id,
                'used_at' => now(),
                'transaction_id' => $transaction->id
            ]);

            $promo->markAsUsed();

            return [
                'success' => true,
                'message' => 'Баланс пополнен на ' . $promo->count_points,
                'balance' => $wallet->getBalanceAttribute(),
                'transaction_id' => $transaction->id
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->errorResponse('Ошибка: ' . $e->getMessage());
        }
    }

    private function handleUnavailablePromo(Promo $promo): array
    {
        if ($promo->isExpired()) {
            return $this->errorResponse('Срок действия промокода истек');
        }

        if ($promo->quantity_left <= 0) {
            return $this->errorResponse('Лимит промокодов исчерпан');
        }

        return $this->errorResponse('Промокод недоступен');
    }

    private function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'message' => $message
        ];
    }
}
