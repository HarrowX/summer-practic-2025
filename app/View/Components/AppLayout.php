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
        $data  = [
            "name" => auth()?->user()?->name,
            "balance" => auth()?->user()?->wallet->balance,
        ];
        return view('layouts.app', $data);
    }
}
