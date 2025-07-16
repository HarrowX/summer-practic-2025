<?php

namespace App\Livewire\Promo;

use Livewire\Component;
use App\Models\Promo;

class AdminPanel extends Component
{
    public $code;
    public $count_points;
    public $quantity_total;
    public $expired_at;

    protected $rules = [
        'code' => 'required|unique:promos|max:50',
        'count_points' => 'required|integer|min:1',
        'quantity_total' => 'required|integer|min:1',
        'expired_at' => 'nullable|date|after:now'
    ];

    public function createPromo()
    {
        $this->validate();

        Promo::create([
            'code' => strtoupper($this->code),
            'count_points' => $this->count_points,
            'quantity_total' => $this->quantity_total,
            'quantity_left' => $this->quantity_total,
            'expired_at' => $this->expired_at
        ]);

        $this->reset();
        session()->flash('message', 'Промокод создан!');
    }

    public function render()
    {
        return view('livewire.promo.admin-panel', [
            'promos' => Promo::latest()->paginate(10)
        ]);
    }
}
