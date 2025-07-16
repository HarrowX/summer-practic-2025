<?php

namespace App\Livewire\Promo;


use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\Promo;
use Bavix\Wallet\Models\Wallet;

class UserPanel extends Component
{
    public $code;
    public $message = '';
    public $balance;

    public function mount()
    {
        $this->refreshBalance();
    }

    public function applyPromo()
    {
        $this->validate(['code' => 'required|string|max:50']);

        $promo = Promo::where('code', $this->code)->first();

        if (!$promo) {
            $this->message = 'Промокод не найден';
            return;
        }

        if ($promo->quantity_left <= 0) {
            $this->message = 'Промокод закончился';
            return;
        }

        if ($promo->expired_at && now()->gt($promo->expired_at)) {
            $this->message = 'Промокод просрочен';
            return;
        }

        if (auth()->user()->promos()->where('promos.id', $promo->id)->exists()) {
            $this->message = 'Вы уже использовали этот промокод';
            return;
        }

        try {
            DB::transaction(function () use ($promo) {
                $wallet = $this->getUserWallet();

                $transaction = $wallet->deposit($promo->count_points, [
                    'description' => 'Активация промокода ' . $promo->code,
                    'meta' => ['promo_id' => $promo->id]
                ]);

                $promo->decrement('quantity_left');

                DB::table('history_promos')->insert([
                    'user_id' => auth()->id(),
                    'promo_id' => $promo->id,
                    'transaction_id' => $transaction->id,
                    'used_at' => now()
                ]);
            });

            $this->refreshBalance();
            $this->message = 'Промокод активирован! +' . $promo->count_points;
            $this->reset('code');

        } catch (\Exception $e) {
            $this->message = 'Ошибка: ' . $e->getMessage();
        }
    }

    protected function getUserWallet(): Wallet
    {
        return auth()->user()->wallet ?:
            auth()->user()->createWallet([
                'name' => 'Основной кошелек',
                'slug' => 'default'
            ]);
    }
    protected function refreshBalance()
    {
        $this->balance = auth()->user()->wallet?->balance ?? 0;
    }

    public function render()
    {
        return view('livewire.promo.user-panel');
    }
}
