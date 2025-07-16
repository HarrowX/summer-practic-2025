<?php

namespace App\Models;

use Bavix\Wallet\Traits\HasWallet;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Bavix\Wallet\Interfaces\Wallet as WalletInterface;


class User extends Authenticatable implements WalletInterface
{
    use HasWallet, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function promos(): BelongsToMany
    {
        return $this->belongsToMany(Promo::class, 'history_promos')
            ->using(PromoUserPivot::class)
            ->withPivot(['transaction_id', 'used_at']);
    }
}
