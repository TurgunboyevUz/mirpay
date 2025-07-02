<?php
namespace TurgunboyevUz\Mirpay\Models;

use Illuminate\Database\Eloquent\Model;
use TurgunboyevUz\Mirpay\Enums\MirpayState;

class MirpayTransaction extends Model
{
    protected $table    = 'mirpay_transactions';
    
    protected $fillable = [
        'transaction_id',
        'amount',
        'state',
    ];

    protected $casts = [
        'state' => MirpayState::class,
    ];

    public function payable()
    {
        return $this->morphTo();
    }
}
