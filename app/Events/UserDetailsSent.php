<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserDetailsSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $code;
    public $name;
    public $balance;

    public function __construct($code, $name, $balance)
    {
        $this->code = $code;
        $this->name = $name;
        $this->balance = (float) $balance;

        Log::info("Создано событие UserDetailsSent", [
            'code' => $code,
            'channel' => 'launcher.'.$code
        ]);
    }

    public function broadcastOn()
    {
        return [
            new Channel('launcher.' . $this->code)
        ];
    }

    public function broadcastAs()
    {
        return 'user.details';
    }

    public function broadcastWith()
    {
        return [
            'name' => $this->name,
            'balance' => $this->balance
        ];
    }
}
