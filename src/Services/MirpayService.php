<?php
namespace TurgunboyevUz\Mirpay\Services;

use Illuminate\Support\Facades\Http;
use TurgunboyevUz\Mirpay\Exceptions\MirpayException;

class MirpayService
{
    public $token = null;

    public const BASE_URL = 'https://mirpay.uz/api/';

    public function __construct()
    {
        $this->token = $this->getToken();
    }

    public function getToken()
    {
        if (isset($this->token)) {
            return $this->token;
        }

        $response = Http::post(self::BASE_URL . 'connect', [
            'api_key' => config('mirpay.secret_key'),
            'kassaid' => config('mirpay.merchant_id'),
        ]);

        if ($response->json('errors')) {
            throw new MirpayException($response->json('errors'));
        }

        return $response->json('token');
    }

    public function createPayment($summa, $info_pay)
    {
        $response = Http::withToken($this->getToken())
            ->post(self::BASE_URL . 'create-pay', [
                'summa'    => $summa,
                'info_pay' => $info_pay,
            ]);

        if ($response->json('natija') != 'Muvaffaqiyatli' or $response->json('payinfo.summa') != $summa) {
            throw new MirpayException('Can not create payment transaction!');
        }

        return [
            'transaction_id' => $response->json('id'),
            'amount'         => $response->json('payinfo.summa'),
            'redirect_url'   => $response->json('payinfo.redicet_url'),
        ];
    }

    public function checkout($id)
    {
        $response = Http::withToken($this->getToken())
            ->post(self::BASE_URL . 'pay/invoice', [
                'payid' => $id,
            ]);

        if ($response->json('payinfo') == null) {
            throw new MirpayException('Can not checkout payment transaction!');
        }

        return [
            'id'         => $response->json('payinfo.payid'),
            'amount'     => $response->json('payinfo.summa'),
            'comment'    => $response->json('payinfo.comment'),
            'status'     => $response->json('payinfo.status'),
            'created_at' => $response->json('payinfo.sana'),
        ];
    }

    public function balance()
    {
        $response = Http::withToken($this->getToken())
            ->get(self::BASE_URL . 'balans');

        if ($response->json('balans') === null) {
            throw new MirpayException('Can not get balance!');
        }

        return $response->json('balans');
    }
}
