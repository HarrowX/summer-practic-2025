<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'count_points',
        'quantity_total',
        'quantity_left',
        'expired_at'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'history_promos')
            ->using(PromoUserPivot::class)
            ->withPivot(['transaction_id', 'used_at']);
    }
}
