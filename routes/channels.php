<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\GameSession;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('launcher.{code}', function ($user, $code) {

    $session = GameSession::where('code', $code)->first();

    if (!$session) {
        return false;
    }

    return [
        'id' => $user?->id,
        'session_id' => $session->id
    ];
});
