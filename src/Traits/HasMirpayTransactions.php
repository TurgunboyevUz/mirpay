<?php
namespace TurgunboyevUz\Mirpay\Traits;

use TurgunboyevUz\Mirpay\Enums\MirpayState;
use TurgunboyevUz\Mirpay\Exceptions\MirpayException;
use TurgunboyevUz\Mirpay\Models\MirpayTransaction;
use TurgunboyevUz\Mirpay\Services\MirpayService;

trait HasMirpayTransactions
{
    public function mirpayTransactions()
    {
        return $this->morphMany(config('mirpay.transaction_model', MirpayTransaction::class), 'payable');
    }

    public function createMirpayTransaction(int $amount, string $comment = '')
    {
        $mirpayService = new MirpayService();
        $transaction   = $mirpayService->createPayment($amount, $comment);

        $this->mirpayTransactions()
            ->where('state', MirpayState::PENDING)
            ->update(['state' => MirpayState::FAILED]);

        $mirpayTransaction = $this->mirpayTransactions()->create([
            'transaction_id' => $transaction['transaction_id'],
            'amount'         => $amount,
            'state'          => MirpayState::PENDING,
        ]);

        $mirpayTransaction->redirect_url = $transaction['redirect_url'];

        return $mirpayTransaction;
    }

    public function checkoutMirpayTransaction()
    {
        $mirpayTransaction = $this->mirpayTransactions()->where('state', MirpayState::PENDING)->first();

        if (! $mirpayTransaction) {
            return false;
        }

        $states = [
            'Jarayonda'       => MirpayState::PENDING,
            'Bekor qilingan!' => MirpayState::FAILED,
            'Muvaffaqiyatli'  => MirpayState::SUCCESS,
        ];

        $mirpayService = new MirpayService();
        $checkout      = $mirpayService->checkout($mirpayTransaction->transaction_id);
        $state         = $states[$checkout['status']] ?? null;

        if (! $state) {
            throw new MirpayException($checkout['status'] . ' state not found');
        }

        $mirpayTransaction->state = $state;
        $mirpayTransaction->save();

        $model = $this;

        if ($state == MirpayState::SUCCESS) {
            require_once app_path('Payment/after_success.php');
        }

        if ($state == MirpayState::FAILED) {
            require_once app_path('Payment/after_fail.php');
        }

        return $state;
    }
}
