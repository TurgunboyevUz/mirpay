<?php
namespace TurgunboyevUz\Mirpay\Models;

use Illuminate\Database\Eloquent\Model;
use TurgunboyevUz\Mirpay\Enums\MirpayState;

class MirpayTransaction extends Model
{
    public ?string $redirect_url = null;

    protected $table    = 'mirpay_transactions';
    protected $guarded = [];

    protected $casts = [
        'state' => MirpayState::class,
    ];

    public function payable()
    {
        return $this->morphTo();
    }
}
