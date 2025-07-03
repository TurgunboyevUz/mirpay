<?php
namespace TurgunboyevUz\Mirpay\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TurgunboyevUz\Mirpay\Enums\MirpayState;
use TurgunboyevUz\Mirpay\Exceptions\MirpayException;
use TurgunboyevUz\Mirpay\Models\MirpayTransaction;
use TurgunboyevUz\Mirpay\Requests\ReceiveTransactionRequest;

class MirpayController extends Controller
{
    public function success(ReceiveTransactionRequest $request)
    {
        $data = $request->validated();

        $transaction = MirpayTransaction::where('transaction_id', $data['payid'])->first();

        if (! $transaction) {
            throw new MirpayException('Transaction not found', Response::HTTP_NOT_FOUND);
        }

        if ($transaction->state !== MirpayState::PENDING) {
            throw new MirpayException('Transaction already has been accepted', Response::HTTP_CONFLICT);
        }

        if ($data['summa'] != $transaction->amount) {
            throw new MirpayException('Transaction amounts do not match', Response::HTTP_CONFLICT);
        }

        $transaction->state = 1;
        $transaction->save();

        $model = $transaction->payable;

        require_once app_path('Payment/after_success.php');

        return response()->json([
            'success' => true,
            'message' => 'Transaction accepted',
        ]);
    }

    public function fail(ReceiveTransactionRequest $request)
    {
        $data = $request->validated();

        $transaction = MirpayTransaction::where('transaction_id', $data['payid'])->first();

        if (! $transaction) {
            throw new MirpayException('Transaction not found', Response::HTTP_NOT_FOUND);
        }

        if ($transaction->state !== MirpayState::PENDING) {
            throw new MirpayException('Transaction already has been accepted', Response::HTTP_CONFLICT);
        }

        if ($data['summa'] != $transaction->amount) {
            throw new MirpayException('Transaction amounts do not match', Response::HTTP_CONFLICT);
        }

        $transaction->state = 0;
        $transaction->save();

        $model = $transaction->payable;

        require_once app_path('Payment/after_fail.php');

        return response()->json([
            'success' => true,
            'message' => 'Transaction accepted',
        ]);
    }
}
