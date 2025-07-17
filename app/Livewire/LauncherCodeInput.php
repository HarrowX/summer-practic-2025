<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GameSession;
use App\Events\UserDetailsSent;
use Pusher\Pusher;

class LauncherCodeInput extends Component
{
    public $code = '';
    public $success = false;
    public $error = '';


    private function getPusherInstance()
    {
        return new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );
    }

    public function submitCode()
    {
        $this->error = ''; // Просто очищаем ошибку напрямую

        $session = GameSession::where('code', $this->code)->first();

        if (!$session) {
            $this->error = 'Неверный код';
            return;
        }

        event(new UserDetailsSent(
            $session->code,
            auth()->user()->name,
            auth()->user()->balance
        ));

        $this->success = true;
    }

    public function render()
    {
        return view('livewire.launcher-code-input');
    }
}
