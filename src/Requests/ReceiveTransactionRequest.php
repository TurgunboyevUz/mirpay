<?php

namespace TurgunboyevUz\Mirpay\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceiveTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payid' => 'required|string|exists:mirpay_transactions,transaction_id',
            'summa' => 'required|numeric',
        ];
    }
}
