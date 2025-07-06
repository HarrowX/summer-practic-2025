<?php

namespace App\View\Components;

use App\Models\User;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */


    public function render(): View
    {
        dd([
            "username" => auth()?->user(),
            "balance" => auth()?->user()?->wallet->balance,
        ]);
        return view('layouts.app', [
            "username" => auth()?->user()?->getAuthIdentifierName(),
            "balance" => auth()?->user()?->wallet->balance,
            ]);
    }
}
