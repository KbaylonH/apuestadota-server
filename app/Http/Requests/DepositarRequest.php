<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class DepositarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'proveedor' => 'required',
            'monto' => 'required|numeric|min:10',
            'ref_code' => 'nullable',
            'transaction_id' => 'required_if:proveedor,paypal'
        ];
    }

    public function messages(){
        return [
            'proveedor.required'=>'El proveedor es requerido',
            'monto.required'=>'El monto es requerido',
            'monto.numeric'=>'El monto debe ser un valor númerico',
            'monto.min'=>'El monto mínimo es de $10',
            'transaction_id.required_if'=>'El ID de transacción es requerido si el depósito es por PayPal'
        ];
    }

    public function failedValidation(Validator $validator) { 
        $errors = [];
        foreach($validator->errors()->all() as $error){
            array_push($errors, $error);
        }
        
        if( !request()->expectsJson() )
            throw new HttpResponseException(redirect()->away(config('app.url_payment_error') . '?error=' . urlencode(implode(",", $errors))));
        else
            throw new HttpResponseException(response()->json(['error'=>implode(',', $errors)], 422)); 
    }
}
