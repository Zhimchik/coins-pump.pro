<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class PumpCoin extends Model
{
    use Notifiable;

    protected $table = 'pump_coins';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pairs', 'purchase', 'for_purchase','for_sale', 'high', 'low', 'avg', 'vol', 'vol_last', 'buy', 'sell'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
