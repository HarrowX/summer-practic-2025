<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Events\UserDetailsSent;
use Pusher\Pusher;

class LauncherController extends Controller
{
    private function getPusherInstance()
    {
        return new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );
    }

    public function generateCode()
    {
        do {
            $code = Str::upper(Str::random(5));
        } while (GameSession::where('code', $code)->exists());

        GameSession::create(['code' => $code]);

        return response()->json(['code' => $code]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|size:5']);

        $session = GameSession::where('code', $request->code)->firstOrFail();
        $user = auth()->user();

        $pusher = $this->getPusherInstance();
        $pusher->trigger(
            'launcher.'.$session->code,
            'user.details',
            [
                'name' => $user->name,
                'balance' => $user->balance,
                'via' => 'direct_pusher'
            ]
        );

        return response()->json(['success' => true]);
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'code' => 'required|size:5',
            'cost' => 'required|integer|min:1'
        ]);

        $session = GameSession::where('code', $request->code)
            ->where('is_active', true)
            ->firstOrFail();

        $user = $session->user;

        // Проверяем баланс
        if ($user->balance < $request->cost) {
            return response()->json(['error' => 'Insufficient balance'], 400);
        }

        // Списание средств
        $user->decrement('balance', $request->cost);
        $session->increment('cost', $request->cost);

        return response()->json([
            'success' => true,
            'new_balance' => $user->balance
        ]);
    }
}
