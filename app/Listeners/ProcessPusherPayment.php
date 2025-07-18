<?php
namespace App\Listeners;

use App\Models\GameSession;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessPusherPayment implements ShouldQueue
{
    public function handle($event)
    {
        // Проверяем префикс события
        if (!str_starts_with($event->name, 'client-')) {
            return;
        }

        $data = $event->data;
        $code = str_replace('launcher.', '', $event->channel);

        // Находим сессию
        $session = GameSession::where('code', $code)
            ->where('is_active', true)
            ->firstOrFail();

        $user = $session->user;

        // Проверяем баланс
        if ($user->balance < $data['cost']) {
            \Log::warning("Insufficient balance for user: {$user->id}");
            return;
        }

        // Списание средств
        $user->decrement('balance', $data['cost']);
        $session->increment('cost', $data['cost']);

        \Log::info("Payment processed", [
            'user' => $user->id,
            'amount' => $data['cost'],
            'new_balance' => $user->balance
        ]);
    }
}
