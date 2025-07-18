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

    public function submitCode()
    {
        $this->error = '';

        $session = GameSession::where('code', $this->code)->first();

        $session->user_id = auth()->id();

        $session->save();

        if (!$session) {
            $this->error = 'Неверный код';
            return;
        }

        UserDetailsSent::dispatch(
            $session->code,
            auth()->user()->name,
            auth()->user()->balance
        );

        $this->success = true;
    }

    public function render()
    {
        return view('livewire.launcher-code-input');
    }
}
