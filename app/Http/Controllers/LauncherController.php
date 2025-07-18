<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Events\UserDetailsSent;
use Pusher\Pusher;
use function Symfony\Component\String\s;

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
        $session->user_id=auth()->id();
        $session->save();


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

        $session = GameSession::where('code', $request->code)->firstOrFail();
        $user = User::findOrFail($session->user_id);

        if ($user->balance < $request->cost) {
            return response()->json([
                'success' => false,
                'error' => 'Недостаточно средств на балансе'
            ], 400);
        }

        // Списание средств
        $transaction = $user->withdraw($request->cost, [
            'session' => $session->code,
            'description' => 'Game payment'
        ]);




        return response()->json([
            'success' => true,
            'new_balance' => $user->balance
        ]);
    }
}
